<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Helpers\DocumentHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class BgReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->type == 'transactions') {
                $query = BgSubmission::with(['recommendation.customer'])
                            ->orderBy('created_at', 'desc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('date', fn($row) => $row->created_at->format('d M Y'))
                    ->addColumn('form_code', fn($row) => '<span class="fw-bold text-primary">'.$row->form_code.'</span>')
                    ->addColumn('customer', fn($row) => $row->recommendation->customer->name ?? '-')
                    ->addColumn('status', function($row){
                        $cls = match($row->status) {
                            'completed' => 'success',
                            'uploaded' => 'info',
                            'pending_print' => 'warning',
                            default => 'secondary'
                        };
                        return '<span class="badge bg-'.$cls.'">'.str_replace('_', ' ', strtoupper($row->status)).'</span>';
                    })
                    ->addColumn('action', function($row) {
                        return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ph-bold ph-printer me-1"></i> Print
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download', ['id' => $row->id, 'doc_type' => 'lampiran_d']).'" target="_blank">
                                        <i class="ph-bold ph-file-text text-warning me-2"></i> Lampiran D
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download', ['id' => $row->id, 'doc_type' => 'submission_form']).'" target="_blank">
                                        <i class="ph-bold ph-file-pdf text-danger me-2"></i> Formulir Pengajuan
                                    </a>
                                </li>
                            </ul>
                        </div>';
                    })
                    ->rawColumns(['form_code', 'status', 'action'])
                    ->make(true);
            }

            if ($request->type == 'expiring') {
                $query = BankGaransi::with('customer')
                            ->where('status', '!=', 'returned')
                            ->orderBy('exp_date', 'asc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('bg_number', fn($row) => $row->bg_number)
                    ->addColumn('customer', fn($row) => $row->customer->name ?? '-')
                    ->addColumn('exp_date', fn($row) => date('d M Y', strtotime($row->exp_date)))
                    ->addColumn('nominal', fn($row) => 'Rp ' . number_format($row->bg_nominal, 0, ',', '.'))
                    ->addColumn('action', function($row) {
                        return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ph-bold ph-envelope-open me-1"></i> Letters
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download-letters', ['id' => $row->id, 'letter_type' => 'distributor']).'" target="_blank">
                                        <i class="ph-bold ph-buildings text-primary me-2"></i> Surat Distributor
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download-letters', ['id' => $row->id, 'letter_type' => 'bank']).'" target="_blank">
                                        <i class="ph-bold ph-bank text-success me-2"></i> Surat Bank
                                    </a>
                                </li>
                            </ul>
                        </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }

        return view('page.bg.bg_reports.index');
    }

    /**
     * Generate PDF untuk Transaction Documents (Lampiran D & Submission)
     */
    public function downloadDoc($id, $doc_type)
    {
        try {
            $submission = BgSubmission::with(['recommendation.customer', 'recommendation.periods'])->findOrFail($id);
            $rec = $submission->recommendation;
            $customer = $rec->customer;
            $financeUser = User::role('manager-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Manager Finance';
            $salesUser = User::role('head-SNM')->first();
            $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

            // Generate Data Umum
            $nomorPkd = DocumentHelper::generatePKDNumber($rec->id, $customer->name, $submission->created_at);

            if ($doc_type == 'lampiran_d') {
                $data = [
                    'submission' => $submission,
                    'rec' => $rec,
                    'customer' => $customer,
                    'nomor_pkd' => $nomorPkd,
                    'finance_name' => $financeName,
                    'sales_name' => $salesName
                ];
                $pdf = Pdf::loadView('pdf.lampiran_d', $data);
                return $pdf->stream('Lampiran_D_'.$submission->form_code.'.pdf');
            }

            if ($doc_type == 'submission_form') {
                // Cari BG terkait (Draft/Submitted)
                $bg = BankGaransi::where('customer_id', $customer->id)
                        ->where('created_at', '>=', $submission->created_at->subDay())
                        ->with('details')
                        ->latest()
                        ->first();

                // Fallback jika BG belum tercreate (misal baru tahap recommendation)
                if (!$bg) return abort(404, 'Data Rincian Bank belum tersedia (Formulir belum diisi customer).');

                $data = [
                    'submission' => $submission,
                    'customer' => $customer,
                    'bg' => $bg
                ];
                $pdf = Pdf::loadView('pdf.bg_submission_document', $data);
                return $pdf->stream('Formulir_Pengajuan_'.$submission->form_code.'.pdf');
            }

        } catch (\Exception $e) {
            return abort(500, 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF untuk Expiring Letters (Surat Distributor & Bank)
     */
    public function downloadLetters($id, $letter_type)
    {
        try {
            $bg = BankGaransi::with('customer')->findOrFail($id);
            $cust = $bg->customer;
            $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $cust->name, now());
            $financeUser = User::role('manager-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Manager Finance';
            $salesUser = User::role('head-SNM')->first();
            $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

            $data = [
                'customer' => $cust,
                'bg' => $bg,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $bg->exp_date,
                'bank_name' => $bg->bank_name ?? 'Bank Terkait', // Ambil dari detail jika perlu logic khusus
                'branch_name' => $bg->branch_name ?? '',
                'nominal' => $bg->bg_nominal,
                'finance_name' => $financeName,
                'sales_name' => $salesName
            ];

            $view = ($letter_type == 'distributor') ? 'pdf.surat_distributor' : 'pdf.surat_bank';
            $filename = ucfirst($letter_type) . '_Letter_' . $bg->bg_number . '.pdf';

            $pdf = Pdf::loadView($view, $data);
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return abort(500, 'Gagal generate Surat: ' . $e->getMessage());
        }
    }
}
