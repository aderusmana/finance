<?php

namespace App\Exports;

use App\Models\Customer\CustomerShipTo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerShipToExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        return CustomerShipTo::with(['customer', 'user'])
            ->orderBy('customer_id', 'asc')
            ->orderBy('ship_to_code', 'asc');
    }

    public function map($shipTo): array
    {
        return [
            $shipTo->customer->code ?? '-',
            $shipTo->customer->name ?? '-',
            $shipTo->ship_to_code ?? '-',
            $shipTo->ship_to_name ?? '-',
            $shipTo->ship_to_address_1 ?? '-',
            $shipTo->ship_to_address_2 ?? '-',
            $shipTo->ship_to_address_3 ?? '-',
            $shipTo->ship_to_city ?? '-',
            $shipTo->user->name ?? ($shipTo->user->nik ?? '-'),
            $shipTo->created_at ? $shipTo->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'KODE CUSTOMER',
            'NAMA CUSTOMER',
            'KODE SHIP TO',
            'NAMA LOKASI SHIP TO / OUTLET',
            'ALAMAT 1',
            'ALAMAT 2',
            'ALAMAT 3',
            'KOTA SHIP TO',
            'SALES PIC',
            'TANGGAL DIBUAT',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']], // Violet Purple
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
