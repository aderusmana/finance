<?php

namespace App\Exports;

use App\Models\Customer\DistributorCustomer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LogisticFeeExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function query()
    {
        $query = DistributorCustomer::with(['distributor', 'customer'])
            ->orderBy('distributor_id', 'asc')
            ->orderBy('customer_id', 'asc');

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function map($item): array
    {
        return [
            $item->distributor->code ?? '-',
            $item->distributor->name ?? '-',
            $item->customer->code ?? '-',
            $item->customer->name ?? '-',
            $item->customer->sort_name ?? '-',
            (float) ($item->logistic_fee ?? 0),
            (float) ($item->proposed_fee ?? 0),
            $item->status ?? 'Approved',
            $item->route_to ?? '-',
            $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
            $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'KODE DISTRIBUTOR',
            'NAMA DISTRIBUTOR',
            'KODE CUSTOMER',
            'NAMA CUSTOMER',
            'SORT NAME CUSTOMER',
            'LOGISTIC FEE AKTIF (IDR)',
            'PROPOSED FEE (IDR)',
            'STATUS',
            'ROUTE TO / APPROVER',
            'TANGGAL DIBUAT',
            'TERAKHIR DIUBAH',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '059669']], // Emerald Green
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
