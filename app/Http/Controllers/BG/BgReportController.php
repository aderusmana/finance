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
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class BgReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // --- TAB 1: TRANSACTION DOCUMENTS ---
            if ($request->type == 'transactions') {
                $query = BgSubmission::with(['recommendation.customer'])
                            ->orderBy('created_at', 'desc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    // CHECKBOX COLUMN
                    ->addColumn('checkbox', function($row) {
                        return '<div class="form-check text-center">
                                    <input class="form-check-input dt-checkbox" type="checkbox" value="'.$row->id.'">
                                </div>';
                    })
                    ->addColumn('date', fn($row) => $row->created_at->format('d M Y'))
                    ->addColumn('form_code', fn($row) => '<span class="fw-bold text-primary">'.$row->form_code.'</span>')
                    ->addColumn('customer', fn($row) => $row->recommendation->customer->name ?? '-')
                    ->addColumn('status', function($row){
                        $status = $row->status;

                        // Default values
                        $color = 'secondary';
                        $icon = 'ph-minus';

                        // Logic Pemilihan Warna & Ikon (Dipindah dari JS ke Sini)
                        switch ($status) {
                            case 'completed':
                                $color = 'success';
                                $icon = 'ph-check-circle';
                                break;
                            case 'uploaded':
                                $color = 'info';
                                $icon = 'ph-upload-simple';
                                break;
                            case 'pending_print':
                                $color = 'warning';
                                $icon = 'ph-printer';
                                break;
                            case 'reviewed':
                                $color = 'primary';
                                $icon = 'ph-paper-plane-right';
                                break;
                            case 'awaiting_upload':
                                $color = 'info';
                                $icon = 'ph-clock';
                                break;
                            case 'waiting_approval':
                                $color = 'warning';
                                $icon = 'ph-hourglass';
                                break;
                            case 'rejected_by_finance':
                                $color = 'danger';
                                $icon = 'ph-x-circle';
                                break;
                        }

                        $label = strtoupper(str_replace('_', ' ', $status));

                        return '<span class="badge bg-'.$color.' status-badge-lg">
                                    <i class="ph-bold '.$icon.' me-1"></i>'.$label.'
                                </span>';
                    })
                    ->addColumn('action', function($row) {
                        return '
                        <div class="dropdown">
                            <button class="btn btn-light-danger dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown"
                                    data-bs-boundary="viewport"
                                    aria-expanded="false">
                                <i class="ph-bold ph-printer"></i> Print
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download', ['id' => $row->id, 'doc_type' => 'lampiran_d']).'" target="_blank">
                                        <i class="ph-duotone ph-file-text text-warning me-2 fs-5"></i> Lampiran D
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download', ['id' => $row->id, 'doc_type' => 'submission_form']).'" target="_blank">
                                        <i class="ph-duotone ph-file-pdf text-danger me-2 fs-5"></i> Formulir Pengajuan
                                    </a>
                                </li>
                            </ul>
                        </div>';
                    })
                    ->rawColumns(['checkbox', 'form_code', 'status', 'action'])
                    ->make(true);
            }

            // --- TAB 2: EXPIRING LETTERS ---
            if ($request->type == 'expiring') {
                $query = BankGaransi::with('customer')->where('status', '!=', 'returned')->orderBy('exp_date', 'asc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    // CHECKBOX COLUMN
                    ->addColumn('checkbox', function($row) {
                        return '<div class="form-check text-center">
                                    <input class="form-check-input dt-checkbox" type="checkbox" value="'.$row->id.'">
                                </div>';
                    })
                    ->addColumn('bg_number', fn($row) => '<span class="text-primary fw-bold">'.$row->bg_number.'</span>')
                    ->addColumn('customer', fn($row) => $row->customer->name ?? '-')
                    ->addColumn('exp_date', fn($row) => date('d M Y', strtotime($row->exp_date)))
                    ->addColumn('nominal', fn($row) => 'Rp ' . number_format($row->bg_nominal, 0, ',', '.'))
                    ->addColumn('action', function($row) {
                        return '
                        <div class="dropdown">
                            <button class="btn btn-light-danger dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown"
                                    data-bs-boundary="viewport"
                                    aria-expanded="false">
                                <i class="ph-bold ph-envelope-open"></i> Letters
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download-letters', ['id' => $row->id, 'letter_type' => 'distributor']).'" target="_blank">
                                        <i class="ph-duotone ph-buildings text-primary me-2 fs-5"></i> Surat Distributor
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="'.route('bg-reports.download-letters', ['id' => $row->id, 'letter_type' => 'bank']).'" target="_blank">
                                        <i class="ph-duotone ph-bank text-success me-2 fs-5"></i> Surat Bank
                                    </a>
                                </li>
                            </ul>
                        </div>';
                    })
                    ->rawColumns(['checkbox', 'bg_number', 'action'])
                    ->make(true);
            }
        }
        return view('page.bg.bg_reports.index');
    }

    // --- HELPER: Siapkan Data View ---
    private function prepareViewData($id, $doc_type, $category)
    {
        $financeUser = User::role('manager-finance')->first();
        $financeName = $financeUser ? $financeUser->name : 'Manager Finance';
        $salesUser = User::role('head-SNM')->first();
        $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

        if ($category == 'transactions') {
            $submission = BgSubmission::with(['recommendation.customer', 'recommendation.periods'])->find($id);
            if (!$submission) return null;

            $rec = $submission->recommendation;
            $customer = $rec->customer;
            $nomorPkd = DocumentHelper::generatePKDNumber($rec->id, $customer->name, $submission->created_at);

            if ($doc_type == 'lampiran_d') {
                return [
                    'view' => 'pdf.lampiran_d',
                    'data' => compact('submission', 'rec', 'customer', 'nomorPkd', 'financeName', 'salesName'),
                    'filename' => 'Lampiran_D_' . str_replace('/', '-', $submission->form_code) . '.pdf'
                ];
            } elseif ($doc_type == 'submission_form') {
                $bg = BankGaransi::where('customer_id', $customer->id)
                        ->where('created_at', '>=', $submission->created_at->subDay())
                        ->with('details')->latest()->first();
                if (!$bg) return null;

                return [
                    'view' => 'pdf.bg_submission_document',
                    'data' => compact('submission', 'customer', 'bg', 'financeName', 'salesName'),
                    'filename' => 'Formulir_' . str_replace('/', '-', $submission->form_code) . '.pdf'
                ];
            }
        }
        elseif ($category == 'expiring') {
            $bg = BankGaransi::with('customer')->find($id);
            if (!$bg) return null;

            $cust = $bg->customer;
            $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $cust->name, now());
            $data = [
                'customer' => $cust, 'bg' => $bg, 'nomor_pkd' => $nomorPkd,
                'expired_date' => $bg->exp_date,
                'bank_name' => $bg->bank_name ?? 'Bank Terkait',
                'branch_name' => $bg->branch_name ?? '',
                'nominal' => $bg->bg_nominal,
                'finance_name' => $financeName, 'sales_name' => $salesName
            ];

            $view = ($doc_type == 'distributor') ? 'pdf.surat_distributor' : 'pdf.surat_bank';
            $prefix = ($doc_type == 'distributor') ? 'Surat_Distributor_' : 'Surat_Bank_';

            return [
                'view' => $view,
                'data' => $data,
                'filename' => $prefix . $bg->bg_number . '_' . str_replace(' ', '_', $cust->name) . '.pdf'
            ];
        }
        return null;
    }

    // --- BULK DOWNLOAD (LOGIC BARU) ---
    public function bulkDownload(Request $request)
    {
        $ids = $request->input('ids');
        $docType = $request->input('doc_type');
        $category = $request->input('category');
        $outputMode = $request->input('output_mode', 'zip'); // 'zip' or 'merged'

        if (empty($ids) || !is_array($ids)) return back()->with('error', 'Tidak ada data dipilih.');

        // ---------------------------------------------------------
        // OPSI 1: DOWNLOAD AS MERGED PDF (1 File, Multiple Pages)
        // ---------------------------------------------------------
        if ($outputMode == 'merged') {
            $mergedHtml = '<html><head><style>
                body { font-family: Arial, sans-serif; }
                .page-break { page-break-after: always; }
                /* Copy CSS penting dari view asli ke sini jika perlu */
            </style></head><body>';

            $count = 0;
            foreach ($ids as $id) {
                $info = $this->prepareViewData($id, $docType, $category);
                if ($info) {
                    // Render View jadi String HTML
                    $viewHtml = view($info['view'], $info['data'])->render();

                    // Bersihkan tag HTML/BODY dari view anak agar tidak double declaration
                    // Kita ambil isinya saja (di dalam body)
                    preg_match('/<body[^>]*>(.*?)<\/body>/is', $viewHtml, $matches);
                    $bodyContent = $matches[1] ?? $viewHtml; // Fallback ke full html jika regex gagal

                    // Tambahkan Page Break (Kecuali item terakhir)
                    $mergedHtml .= '<div class="document-wrapper">';
                    $mergedHtml .= $bodyContent;
                    $mergedHtml .= '</div>';

                    if ($count < count($ids) - 1) {
                        $mergedHtml .= '<div class="page-break"></div>';
                    }
                    $count++;
                }
            }
            $mergedHtml .= '</body></html>';

            $filename = 'Merged_' . ucfirst($docType) . '_' . date('Ymd_His') . '.pdf';
            return Pdf::loadHTML($mergedHtml)->stream($filename);
        }

        // ---------------------------------------------------------
        // OPSI 2: DOWNLOAD AS ZIP (Multiple Files)
        // ---------------------------------------------------------
        else {
            $zipFileName = 'Bulk_' . ucfirst($docType) . '_' . date('Ymd_His') . '.zip';
            $zipPath = storage_path('app/public/temp_zip/' . $zipFileName);

            if (!file_exists(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($ids as $id) {
                    $info = $this->prepareViewData($id, $docType, $category);
                    if ($info) {
                        $pdfContent = Pdf::loadView($info['view'], $info['data'])->output();
                        $zip->addFromString($info['filename'], $pdfContent);
                    }
                }
                $zip->close();
            }
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
    }

    // --- Single Download Wrapper ---
    public function downloadDoc($id, $doc_type) {
        $info = $this->prepareViewData($id, $doc_type, 'transactions');
        if(!$info) abort(404);
        $pdf = Pdf::loadView($info['view'], $info['data']);
        return $pdf->stream($info['filename']);
    }

    public function downloadLetters($id, $letter_type) {
        $info = $this->prepareViewData($id, $letter_type, 'expiring');
        if(!$info) abort(404);
        $pdf = Pdf::loadView($info['view'], $info['data']);
        return $pdf->stream($info['filename']);
    }
}
