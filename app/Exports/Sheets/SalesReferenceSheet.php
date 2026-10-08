<?php

namespace App\Exports\Sheets;

use App\Models\Customer\Sales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReferenceSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Sales & Users Reference';
    }

    public function collection()
    {
        return Sales::with(['user', 'accountGroup', 'branch', 'region'])
            ->orderBy('user_id', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'USER ID (user_id)',
            'SALES / USER NAME',
            'EMAIL',
            'REGION',
            'BRANCH',
            'ACCOUNT GROUP',
            'INSTRUCTIONS',
        ];
    }

    public function map($item): array
    {
        return [
            $item->user_id,
            $item->user->name ?? '-',
            $item->user->email ?? '-',
            $item->region->region_name ?? '-',
            $item->branch->branch_name ?? '-',
            $item->accountGroup->name_account_group ?? '-',
            'Use number ' . $item->user_id . ' in the user_id column on Sheet 1 if assigning this sales representative',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6366F1'] // Indigo Purple
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
