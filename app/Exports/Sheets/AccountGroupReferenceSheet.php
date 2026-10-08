<?php

namespace App\Exports\Sheets;

use App\Models\Customer\AccountGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountGroupReferenceSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Account Group Reference';
    }

    public function collection()
    {
        return AccountGroup::orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID (account_group)',
            'ACCOUNT GROUP / REGION NAME',
            'DEFAULT BANK GUARANTEE',
            'DEFAULT CCAR',
            'INSTRUCTIONS',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name_account_group ?? '-',
            $item->bank_garansi ? 'YES' : 'NO',
            $item->ccar ?? 'smd_idr',
            'Use number ' . $item->id . ' in the account_group column on Sheet 1 ("Import Template")',
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
                    'startColor' => ['rgb' => 'D97706'] // Amber / Orange
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
