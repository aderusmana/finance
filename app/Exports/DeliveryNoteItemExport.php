<?php

namespace App\Exports;

use App\Models\Customer\LogisticOrderItem;
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

    public function __construct(?string $dateFrom = null, ?string $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
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
                'customer_ship_toes.ship_to_name as ship_to',
                'logistic_order_items.order_item_code as item_code',
                'logistic_order_items.order_item_name as item_name',
                'logistic_order_items.order_quantity as qty',
                'logistic_order_items.order_amount as total',
                'logistic_orders.delivery_date as delivery_date',
            ])
            ->orderBy('logistic_orders.delivery_date', 'desc')
            ->orderBy('delivery_order_notes.delivery_order_no', 'desc');

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('logistic_orders.delivery_date', [$this->dateFrom, $this->dateTo]);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'DN NO',
            'CUSTOMER CODE',
            'CUSTOMER NAME',
            'DISTRIBUTOR CODE',
            'DISTRIBUTOR NAME',
            'SHIP TO',
            'PRICE ITEM',
            'ITEM CODE',
            'ITEM NAME',
            'QTY',
            'TOTAL',
        ];
    }

    public function map($row): array
    {
        $qty = (float) ($row->qty ?? 0);
        $total = (float) ($row->total ?? 0);
        $priceItem = $qty > 0 ? ($total / $qty) : 0;

        $customerCode = $row->customer_code ?: '-';

        return [
            $row->dn_no ?? '-',
            $customerCode,
            $row->customer_name ?? '-',
            $row->distributor_code ?? '-',
            $row->distributor_name ?? '-',
            $row->ship_to ?? '-',
            $priceItem,
            $row->item_code ?? '-',
            $row->item_name ?? '-',
            $qty,
            $total,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 2 rows before headings
                $sheet->insertNewRowBefore(1, 2);

                // Title row
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'Report Logistic Order');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Date range row
                $sheet->mergeCells('A2:K2');
                if (!empty($this->dateFrom) && !empty($this->dateTo)) {
                    $sheet->setCellValue('A2', 'From ' . $this->dateFrom . ' - To ' . $this->dateTo);
                } else {
                    $sheet->setCellValue('A2', 'All Dates');
                }
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Headings styling (now on row 3)
                $sheet->getStyle('A3:K3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '166534']],
                ]);
            },
        ];
    }
}
