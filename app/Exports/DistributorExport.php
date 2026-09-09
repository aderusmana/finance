<?php

namespace App\Exports;

use App\Models\Customer\Distributor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DistributorExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        return Distributor::with('customer')->orderBy('code', 'asc');
    }

    public function map($distributor): array
    {
        return [
            $distributor->code ?? '-',
            $distributor->name ?? '-',
            $distributor->email ?? '-',
            $distributor->customer ? ($distributor->customer->code . ' - ' . $distributor->customer->name) : 'Input Manual (Belum Terhubung)',
            $distributor->customers->count(),
            $distributor->created_at ? $distributor->created_at->format('d/m/Y H:i') : '-',
            $distributor->updated_at ? $distributor->updated_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'KODE DISTRIBUTOR',
            'NAMA DISTRIBUTOR',
            'EMAIL',
            'RELASI MASTER CUSTOMER',
            'TOTAL CUSTOMER/OUTLET TERHUBUNG',
            'TANGGAL DIBUAT',
            'TERAKHIR DIUBAH',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D97706']], // Amber Orange
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
