<?php

namespace App\Exports;

use App\Models\Customer\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function query()
    {
        $query = Customer::with(['accountGroup', 'customerClass', 'sales'])
            ->orderBy('code', 'asc');

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function map($customer): array
    {
        return [
            $customer->code ?? '-',
            $customer->name ?? '-',
            $customer->sort_name ?? '-',
            $customer->customerClass->name_class ?? '-',
            $customer->accountGroup->name_account_group ?? '-',
            $customer->term_of_payment ? $customer->term_of_payment . ' Hari' : '-',
            (float) ($customer->credit_limit ?? 0),
            $customer->bank_garansi ?? 'TIDAK',
            $customer->area ?? '-',
            $customer->city ?? '-',
            $customer->address1 ?? '-',
            $customer->email ?? '-',
            $customer->npwp ?? '-',
            $customer->status ?? 'Active',
            $customer->status_approval ?? '-',
            $customer->join_date ? $customer->join_date->format('d/m/Y') : '-',
            $customer->created_at ? $customer->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'KODE CUSTOMER',
            'NAMA CUSTOMER',
            'SORT NAME',
            'CUSTOMER CLASS',
            'ACCOUNT GROUP / REGION',
            'TERM OF PAYMENT',
            'CREDIT LIMIT (IDR)',
            'BANK GARANSI',
            'AREA',
            'KOTA',
            'ALAMAT',
            'EMAIL',
            'NPWP',
            'STATUS',
            'STATUS APPROVAL',
            'TANGGAL BERGABUNG',
            'DIBUAT PADA',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']], // Indigo-Blue
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
