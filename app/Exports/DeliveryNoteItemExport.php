<?php

namespace App\Exports;

use App\Models\Customer\LogisticOrderItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DeliveryNoteItemExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private ?string $dateFrom;
    private ?string $dateTo;
    private ?string $distributors;

    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?string $distributors = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->distributors = $distributors;
    }

    public function query()
    {
        $query = LogisticOrderItem::query()
            ->join('logistic_orders', 'logistic_orders.id', '=', 'logistic_order_items.logistic_order_id')
            ->join('delivery_order_notes', 'delivery_order_notes.logistic_order_id', '=', 'logistic_orders.id')
            ->leftJoin('customers', 'customers.id', '=', 'logistic_orders.customer_id')
            ->leftJoin('distributors', 'distributors.id', '=', 'logistic_orders.distributor_id')
            ->leftJoin('customer_ship_toes', 'customer_ship_toes.id', '=', 'logistic_orders.customer_ship_to_id')
            ->where('delivery_order_notes.status', 'Downloaded')
            ->select([
                'delivery_order_notes.delivery_order_no as dn_no',
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
            $query->whereIn('logistic_orders.distributor_id', $distArray);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'DN NO',
            'DISTRIBUTOR NAME',
            'CUSTOMER NAME',
            'ITEM NAME',
            'PRICE ITEM',
            'QTY',
            'TOTAL CLAIM',
            'SALES VALUE',
            'RATIO'
        ];
    }

    public function map($row): array
    {
        $qty = (float) ($row->qty ?? 0);
        $total = (float) ($row->total ?? 0);
        $priceItem = $qty > 0 ? ($total / $qty) : 0;

        $priceList = (float) ($row->price_list ?? 0);
        $salesValue = $priceList * $qty;

        $ratio = $salesValue > 0 ? ($total / $salesValue) : 0;

        return [
            $row->dn_no ?? '-',
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
                
                // 1. Tambahkan info unduhan di paling atas (Baris 1 & 2)
                $sheet->insertNewRowBefore(1, 4); // Tambah 4 baris kosong di atas
                
                $sheet->setCellValue('A1', 'Tanggal Download: ' . now()->format('d/m/Y H:i:s'));
                $sheet->setCellValue('A2', 'Dibuat Oleh: ' . Auth::user()->name);
                $sheet->getStyle('A1:A2')->getFont()->setSize(9)->setItalic(true);

                // 2. Judul Laporan (Baris 3 & 4)
                $sheet->mergeCells('A3:I3');
                $sheet->setCellValue('A3', 'Report Logistic Order');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => 'center']
                ]);

                $sheet->mergeCells('A4:I4');
                $range = (!empty($this->dateFrom)) ? "Period: $this->dateFrom - $this->dateTo" : "All Dates";
                $sheet->setCellValue('A4', $range);
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => 'center']
                ]);

                // 3. Styling Heading (Sekarang ada di Baris 5)
                $sheet->getStyle('A5:I5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '166534']],
                ]);

                // 4. Baris TOTAL
                $totalRow = $lastRow + 5; // Menyesuaikan karena ada insert 4 baris di atas
                $dataStartRow = 6;
                $dataEndRow = $totalRow - 1;

                $sheet->setCellValue("F$totalRow", "GRAND TOTAL:");
                $sheet->setCellValue("G$totalRow", "=SUM(G$dataStartRow:G$dataEndRow)");
                $sheet->setCellValue("H$totalRow", "=SUM(H$dataStartRow:H$dataEndRow)");
                $sheet->setCellValue("I$totalRow", "=IF(H$totalRow>0, G$totalRow/H$totalRow, 0)");
                
                $sheet->getStyle("F$totalRow:I$totalRow")->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle("I$totalRow")->getNumberFormat()->setFormatCode('0.00%');

                // 5. Tanda Tangan (3 Kolom: B, E, H)
                $sigRow = $totalRow + 3;
                $sheet->setCellValue("B$sigRow", "Dibuat oleh,");
                $sheet->setCellValue("E$sigRow", "Diketahui oleh,");
                $sheet->setCellValue("H$sigRow", "Disetujui oleh,");

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("B$nameRow", Auth::user()->name);
                $sheet->setCellValue("E$nameRow", "Rofika");
                $sheet->setCellValue("H$nameRow", "Ronal Katili");

                $sheet->getStyle("B$sigRow:H$nameRow")->applyFromArray(['alignment' => ['horizontal' => 'center']]);
                $sheet->getStyle("B$nameRow:H$nameRow")->applyFromArray(['font' => ['bold' => true, 'underline' => true]]);
            },
        ];
    }
}
