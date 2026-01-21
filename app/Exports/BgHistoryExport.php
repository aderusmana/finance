<?php

namespace App\Exports;

use App\Models\BG\BgHistory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BgHistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    // Bisa menerima filter tanggal jika ingin export periode tertentu
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = BgHistory::with(['bankGaransi.customer', 'creator'])
                    ->orderBy('created_at', 'desc');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return $query;
    }

    // Mapping Data dari Database ke Kolom Excel
    public function map($history): array
    {
        // Tentukan Jenis Transaksi berdasarkan logic sederhana
        $type = 'Update/Revision';
        if ($history->new_nominal == 0) $type = 'Released/Returned';
        elseif ($history->previous_nominal == 0) $type = 'New Submission';
        elseif ($history->new_nominal > $history->previous_nominal) $type = 'Increase Limit';
        
        return [
            $history->created_at->format('d M Y H:i'),
            $history->bankGaransi->customer->name ?? '-',
            $history->bankGaransi->bg_number ?? '-',
            $history->bankGaransi->bank_name ?? '-', // Asumsi ada field bank_name di relation atau detail
            $type,
            $history->previous_nominal, // Format angka raw biar bisa di sum di excel
            $history->new_nominal,
            $history->new_exp_date ? \Carbon\Carbon::parse($history->new_exp_date)->format('d M Y') : '-',
            $history->remarks,
            $history->creator->name ?? 'System',
        ];
    }

    public function headings(): array
    {
        return [
            'TANGGAL HISTORY',
            'NAMA CUSTOMER',
            'NO. BANK GARANSI',
            'BANK PENERBIT',
            'JENIS TRANSAKSI',
            'NOMINAL SEBELUM (IDR)',
            'NOMINAL SETELAH (IDR)',
            'JATUH TEMPO',
            'KETERANGAN / REMARKS',
            'DIPROSES OLEH',
        ];
    }

    // Styling Header biar Bold & Warna
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a8a']]],
        ];
    }
}