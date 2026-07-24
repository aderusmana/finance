<?php

namespace App\Exports;

use App\Models\Customer\LogisticOrderItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths; // Menggantikan ShouldAutoSize
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

    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?string $distributors = null, ?string $apNumber = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->distributors = $distributors;
        $this->apNumber = $apNumber;
    }

    public function query()
    {
        $query = LogisticOrderItem::query()
            ->join('logistic_orders', 'logistic_orders.id', '=', 'logistic_order_items.logistic_order_id')
            ->join('delivery_order_notes', 'delivery_order_notes.logistic_order_id', '=', 'logistic_orders.id')
            ->leftJoin('customers', 'customers.id', '=', 'logistic_orders.customer_id')
            ->leftJoin('distributors', 'distributors.id', '=', 'logistic_orders.distributor_id')
            ->leftJoin('customer_ship_toes', 'customer_ship_toes.id', '=', 'logistic_orders.customer_ship_to_id')
            ->leftJoin('distributor_customers', function ($join) {
                $join->on('distributor_customers.distributor_id', '=', 'logistic_orders.distributor_id')
                     ->on('distributor_customers.customer_id', '=', 'logistic_orders.customer_id');
            })
            ->where('delivery_order_notes.status', 'Downloaded')
            ->select([
                'delivery_order_notes.delivery_order_no as dn_no',
                'logistic_orders.no_po as no_po',
                'customers.code as customer_code',
                'customers.name as customer_name',
                'distributors.code as distributor_code',
                'distributors.name as distributor_name',
                'customer_ship_toes.ship_to_code as ship_to_code',
                'customer_ship_toes.ship_to_name as ship_to',
                'logistic_order_items.order_item_code as item_code',
                'logistic_order_items.order_item_name as item_name',
                'logistic_order_items.order_quantity as qty',
                'logistic_order_items.order_amount as total',
                'logistic_order_items.price_list as price_list',
                'logistic_orders.delivery_date as delivery_date',
                'logistic_orders.distributor_id as distributor_id',
                'distributor_customers.proposed_fee as proposed_fee',
            ])
            ->orderBy('logistic_orders.delivery_date', 'desc')
            ->orderBy('delivery_order_notes.delivery_order_no', 'desc');

        $user = Auth::user();
        if (!$user->hasRole(['super-admin', 'sales-ka-approver'])) {
            $query->where('logistic_orders.created_by', $user->id);
        }

        if ($this->dateFrom && $this->dateTo) {
            $query->whereBetween('logistic_orders.delivery_date', [$this->dateFrom, $this->dateTo]);
        }

        if (!empty($this->distributors)) {
            $distArray = is_array($this->distributors) ? $this->distributors : explode(',', $this->distributors);
            $distArray = array_filter($distArray);
            if (count($distArray) > 0) {
                $query->whereIn('logistic_orders.distributor_id', $distArray);
            }
        }

        return $query;
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
            'H' => 12,  // PRICE ITEM
            'I' => 8,   // QTY
            'J' => 16,  // TOTAL CLAIM
            'K' => 16,  // SALES VALUE
            'L' => 10,  // RATIO
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
        $qty = (float) ($row->qty ?? 0);
        $priceItem = (float) ($row->proposed_fee ?? 0);
        $total = $priceItem * $qty;
        $priceList = (float) ($row->price_list ?? 0);
        $salesValue = $priceList * $qty;
        $ratio = $salesValue > 0 ? ($total / $salesValue) : 0;

        $this->sumTotalClaim += $total;
        $this->sumSalesValue += $salesValue;

        return [
            $this->rowIndex++,
            $row->dn_no ?? '-',
            $row->no_po ?? '-',
            $row->delivery_date ? \Carbon\Carbon::parse($row->delivery_date)->format('d/m/Y') : '-',
            $row->distributor_name ?? '-',
            $row->customer_name ?? '-',
            $row->item_name ?? '-',
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
                $sheet->setCellValue('A5', 'AP : ' . strtoupper($this->apNumber));
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
