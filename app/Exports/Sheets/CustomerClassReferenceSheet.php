<?php

namespace App\Exports\Sheets;

use App\Models\Customer\CustomerClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerClassReferenceSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Customer Class Reference';
    }

    public function collection()
    {
        return CustomerClass::orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID (customer_class)',
            'CUSTOMER CLASS NAME',
            'INSTRUCTIONS',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name_class ?? '-',
            'Use number ' . $item->id . ' in the customer_class column on Sheet 1 ("Import Template")',
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
                    'startColor' => ['rgb' => '059669'] // Emerald Green
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
