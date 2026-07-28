<?php

namespace App\Exports;

use App\Models\Customer\LogisticOrderItem;
use App\Models\Customer\DistributorCustomer;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DeliveryNoteItemExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    private ?string $dateFrom;
    private ?string $dateTo;
    private ?string $distributors;
    private float $sumTotalClaim = 0;
    private float $sumSalesValue = 0;
    private int $rowIndex = 1;
    private ?string $apNumber;
    private ?string $statusTab;
    private ?string $searchCustomer;

    private array $feeCache = [];

    public function __construct(
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $distributors = null,
        ?string $apNumber = null,
        ?string $statusTab = 'downloaded',
        ?string $searchCustomer = null
    ) {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->distributors = $distributors;
        $this->apNumber = $apNumber;
        $this->statusTab = $statusTab;
        $this->searchCustomer = $searchCustomer;
    }

    public function query()
    {
        $query = LogisticOrderItem::with([
            'logisticOrder.distributor',
            'logisticOrder.customer',
            'logisticOrder.shipTo',
            'logisticOrder.note'
        ]);

        $statusTab = $this->statusTab ?? 'downloaded';
        $query->whereHas('logisticOrder.note', function ($q) use ($statusTab) {
            if ($statusTab === 'downloaded') {
                $q->where('status', 'Downloaded');
            } else {
                $q->where('status', 'Pending Download');
            }
        });

        $user = Auth::user();
        if (!$user->hasRole(['super-admin', 'sales-ka-approver'])) {
            $query->whereHas('logisticOrder', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereHas('logisticOrder', function ($q) {
                $q->whereBetween('delivery_date', [$this->dateFrom, $this->dateTo]);
            });
        }

        if (!empty($this->distributors)) {
            $distArray = is_array($this->distributors) ? $this->distributors : explode(',', $this->distributors);
            $distArray = array_filter($distArray);
            if (count($distArray) > 0) {
                $query->whereHas('logisticOrder', function ($q) use ($distArray) {
                    $q->whereIn('distributor_id', $distArray);
                });
            }
        }

        if (!empty($this->searchCustomer)) {
            $query->whereHas('logisticOrder.customer', function ($q) {
                $q->where('name', 'LIKE', '%' . $this->searchCustomer . '%');
            });
        }

        $query->join('logistic_orders', 'logistic_orders.id', '=', 'logistic_order_items.logistic_order_id')
            ->select('logistic_order_items.*')
            ->orderBy('logistic_orders.delivery_date', 'desc')
            ->orderBy('logistic_order_items.id', 'asc');

        return $query;
    }

    private function getProposedFee(?int $distributorId, ?int $customerId): float
    {
        if (!$distributorId || !$customerId) {
            return 0;
        }

        $key = "{$distributorId}_{$customerId}";
        if (!array_key_exists($key, $this->feeCache)) {
            $record = DistributorCustomer::where('distributor_id', $distributorId)
                ->where('customer_id', $customerId)
                ->first();

            $this->feeCache[$key] = (float) ($record->proposed_fee ?? 0);
        }

        return $this->feeCache[$key];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 25,  // DN NO
            'C' => 20,  // NO PO
            'D' => 15,  // DELIVERY DATE
            'E' => 30,  // DISTRIBUTOR NAME
            'F' => 30,  // CUSTOMER NAME
            'G' => 30,  // ITEM NAME
            'H' => 15,  // LOGISTIC FEE
            'I' => 8,   // QTY
            'J' => 18,  // TOTAL CLAIM
            'K' => 18,  // SALES VALUE
            'L' => 12,  // RATIO
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'DN NO',
            'NO PO',
            'DELIVERY DATE',
            'DISTRIBUTOR NAME',
            'CUSTOMER NAME',
            'ITEM NAME',
            'LOGISTIC FEE',
            'QTY',
            'TOTAL CLAIM',
            'SALES VALUE',
            'RATIO'
        ];
    }

    public function map($row): array
    {
        $lo = $row->logisticOrder;
        $qty = (float) ($row->order_quantity ?? 0);
        $priceItem = $this->getProposedFee($lo->distributor_id ?? null, $lo->customer_id ?? null);
        $total = $priceItem * $qty;
        $priceList = (float) ($row->price_list ?? 0);
        $salesValue = $priceList * $qty;
        $ratio = $salesValue > 0 ? ($total / $salesValue) : 0;

        $this->sumTotalClaim += $total;
        $this->sumSalesValue += $salesValue;

        return [
            $this->rowIndex++,
            $lo->note->delivery_order_no ?? '-',
            $lo->no_po ?? '-',
            $lo->delivery_date ? \Carbon\Carbon::parse($lo->delivery_date)->format('d/m/Y') : '-',
            $lo->distributor->name ?? '-',
            $lo->customer->name ?? '-',
            $row->order_item_name ?? '-',
            $priceItem,
            $qty,
            $total,
            $salesValue,
            round($ratio * 100, 2) . '%',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $sheet->insertNewRowBefore(1, 5);

                $sheet->setCellValue('A1', 'Tanggal: ' . now()->format('d/m/Y H:i:s'));
                $sheet->setCellValue('A2', 'Dibuat Oleh: ' . Auth::user()->name);
                $sheet->getStyle('A1:A2')->getFont()->setSize(9)->setItalic(true);

                $sheet->mergeCells('A3:L3');
                $sheet->setCellValue('A3', 'Report Logistic Order');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => 'center']
                ]);

                $sheet->mergeCells('A4:L4');
                $range = (!empty($this->dateFrom)) ? "Period: $this->dateFrom - $this->dateTo" : "Periode: All Dates";
                $sheet->setCellValue('A4', $range);
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => 'center']
                ]);

                $sheet->mergeCells('A5:L5');
                $sheet->setCellValue('A5', 'AP : ' . strtoupper($this->apNumber ?? '-'));
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => 'center']
                ]);

                $sheet->getStyle('A6:L6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '166534']],
                ]);

                $totalRow = $lastRow + 6;
                $dataStartRow = 7;
                $dataEndRow = $totalRow - 1;

                if ($dataEndRow < $dataStartRow) {
                    $dataEndRow = $dataStartRow;
                }

                $grandRatio = $this->sumSalesValue > 0 ? ($this->sumTotalClaim / $this->sumSalesValue) : 0;

                $sheet->setCellValue("I$totalRow", "GRAND TOTAL:");
                $sheet->setCellValue("J$totalRow", $this->sumTotalClaim);
                $sheet->setCellValue("K$totalRow", $this->sumSalesValue);
                $sheet->setCellValue("L$totalRow", $grandRatio);

                $sheet->getStyle("I$totalRow:L$totalRow")->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle("J{$dataStartRow}:K{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("L$totalRow")->getNumberFormat()->setFormatCode('0.00%');

                $sheet->getStyle("L{$dataStartRow}:L{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
                ]);

                $sheet->getStyle("A{$dataStartRow}:A{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                ]);
                $sheet->getStyle("I{$dataStartRow}:I{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                ]);

                $sigRow = $totalRow + 3;
                $sheet->setCellValue("B$sigRow", "Dibuat oleh,");
                $sheet->setCellValue("E$sigRow", "Diketahui oleh,");
                $sheet->setCellValue("I$sigRow", "Disetujui oleh,");

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("B$nameRow", Auth::user()->name);
                $sheet->setCellValue("E$nameRow", "Rofika");
                $sheet->setCellValue("I$nameRow", "Ronal Katili");

                $sheet->getStyle("B$sigRow:I$nameRow")->applyFromArray(['alignment' => ['horizontal' => 'center']]);
                $sheet->getStyle("B$nameRow:I$nameRow")->applyFromArray(['font' => ['bold' => true, 'underline' => true]]);
            },
        ];
    }
}

