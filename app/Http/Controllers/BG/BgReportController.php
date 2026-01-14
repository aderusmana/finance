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
                        $color = 'secondary';
                        $icon = 'ph-minus';

                        switch ($status) {
                            case 'completed': $color = 'success'; $icon = 'ph-check-circle'; break;
                            case 'uploaded': $color = 'info'; $icon = 'ph-upload-simple'; break;
                            case 'pending_print': $color = 'warning'; $icon = 'ph-printer'; break;
                            case 'reviewed': $color = 'primary'; $icon = 'ph-paper-plane-right'; break;
                            case 'awaiting_upload': $color = 'info'; $icon = 'ph-clock'; break;
                            case 'waiting_approval': $color = 'warning'; $icon = 'ph-hourglass'; break;
                            case 'rejected_by_finance': $color = 'danger'; $icon = 'ph-x-circle'; break;
                        }
                        $label = strtoupper(str_replace('_', ' ', $status));
                        return '<span class="badge bg-'.$color.' status-badge-lg"><i class="ph-bold '.$icon.' me-1"></i>'.$label.'</span>';
                    })
                    ->addColumn('action', function($row) {
                        return '
                        <div class="dropdown">
                            <button class="btn btn-light-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ph-bold ph-printer"></i> Print
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="'.route('bg-reports.download', ['id' => $row->id, 'doc_type' => 'lampiran_d']).'" target="_blank"><i class="ph-duotone ph-file-text text-warning me-2 fs-5"></i> Lampiran D</a></li>
                                <li><a class="dropdown-item" href="'.route('bg-reports.download', ['id' => $row->id, 'doc_type' => 'submission_form']).'" target="_blank"><i class="ph-duotone ph-file-pdf text-danger me-2 fs-5"></i> Formulir Pengajuan</a></li>
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
                        ->addColumn('checkbox', function($row) {
                        return '<div class="form-check text-center"><input class="form-check-input dt-checkbox" type="checkbox" value="'.$row->id.'"></div>';
                    })
                    ->addColumn('bg_number', fn($row) => '<span class="text-primary fw-bold">'.$row->bg_number.'</span>')
                    ->addColumn('customer', fn($row) => $row->customer->name ?? '-')
                    ->addColumn('exp_date', fn($row) => date('d M Y', strtotime($row->exp_date)))
                    ->addColumn('nominal', fn($row) => 'Rp ' . number_format($row->bg_nominal, 0, ',', '.'))
                    ->addColumn('action', function($row) {
                        return '
                        <div class="dropdown">
                            <button class="btn btn-light-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ph-bold ph-envelope-open"></i> Letters</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="'.route('bg-reports.download-letters', ['id' => $row->id, 'letter_type' => 'distributor']).'" target="_blank"><i class="ph-duotone ph-buildings text-primary me-2 fs-5"></i> Surat Distributor</a></li>
                                <li><a class="dropdown-item" href="'.route('bg-reports.download-letters', ['id' => $row->id, 'letter_type' => 'bank']).'" target="_blank"><i class="ph-duotone ph-bank text-success me-2 fs-5"></i> Surat Bank</a></li>
                            </ul>
                        </div>';
                    })
                    ->rawColumns(['checkbox', 'bg_number', 'action'])
                    ->make(true);
            }
        }
        return view('page.bg.bg_reports.index');
    }

    private function prepareViewData($id, $doc_type, $category)
    {
        $financeUser = User::role('manager-finance')->first();
        $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head';
        $salesUser = User::role('head-SNM')->first();
        $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

        if ($category == 'transactions') {
            $submission = BgSubmission::with(['recommendation.customer', 'recommendation.periods'])->find($id);
            if (!$submission) return null;

            $rec = $submission->recommendation;
            $customer = $rec->customer;
            $nomorPkd = $customer->no_pkd;
            if (empty($nomorPkd)) {
                $nomorPkd = DocumentHelper::generatePKDNumber($rec->id, $customer->name, $submission->created_at);
            }

            $submissionDates = BgSubmission::where('bg_recommendation_id', $rec->id)->pluck('created_at');
            $totalBgDiserahkan = BankGaransi::where('customer_id', $customer->id)
                                    ->whereIn('created_at', $submissionDates)
                                    ->sum('bg_nominal');

            if ($totalBgDiserahkan == 0) {
                $lastBg = BankGaransi::where('customer_id', $customer->id)
                        ->where('status', '!=', 'draft')
                        ->latest()->first();
                $totalBgDiserahkan = $lastBg ? $lastBg->bg_nominal : 0;
            }

            $commonData = [
                'submission' => $submission,
                'rec' => $rec,
                'customer' => $customer,
                'nomor_pkd' => $nomorPkd,
                'total_bg_diserahkan' => $totalBgDiserahkan,
                'finance_name' => $financeName,
                'sales_name' => $salesName,
                'bg' => null
            ];

            if ($doc_type == 'lampiran_d') {
                return [
                    'view' => 'pdf.lampiran_d',
                    'data' => $commonData,
                    'filename' => 'Lampiran_D_' . $submission->form_code . '.pdf'
                ];
            }
            elseif ($doc_type == 'submission_form') {
                $siblings = BgSubmission::where('bg_recommendation_id', $rec->id)
                            ->where('created_at', $submission->created_at)
                            ->orderBy('id', 'asc')->pluck('id')->toArray();

                $myIndex = array_search($submission->id, $siblings);
                $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                                ->where('created_at', $submission->created_at)
                                ->with('details')->orderBy('id', 'asc')->get();

                $bg = isset($candidateBgs[$myIndex]) ? $candidateBgs[$myIndex] : $candidateBgs->first();

                if (!$bg) return null;
                $commonData['bg'] = $bg;

                return [
                    'view' => 'pdf.bg_confirmation',
                    'data' => $commonData,
                    'filename' => 'Formulir_' . $submission->form_code . '.pdf'
                ];
            }
        }
        elseif ($category == 'expiring') {
            $bg = BankGaransi::with('customer')->find($id);
            if (!$bg) return null;

            $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $bg->customer->name, now());

            $data = [
                'customer' => $bg->customer,
                'bg' => $bg,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $bg->exp_date,
                'bank_name' => $bg->bank_name ?? 'Bank Terkait',
                'branch_name' => $bg->branch_name ?? '',
                'nominal' => $bg->bg_nominal,
                'finance_name' => $financeName,
                'sales_name' => $salesName
            ];

            $view = ($doc_type == 'distributor') ? 'pdf.surat_distributor' : 'pdf.surat_bank';
            return [
                'view' => $view, 'data' => $data,
                'filename' => ucfirst($doc_type) . '_' . $bg->bg_number . '.pdf'
            ];
        }
        return null;
    }

    public function bulkDownload(Request $request)
    {
        $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
        $docType = $request->doc_type;
        $category = $request->category;
        $outputMode = $request->output_mode;
        if (empty($ids)) return back()->with('error', 'Tidak ada data dipilih.');
        $baseFileName = 'Bulk_' . ucfirst($docType) . '_' . date('Ymd_His');
        if ($outputMode == 'merged') {
            $dataset = [];

            foreach ($ids as $id) {
                $info = $this->prepareViewData($id, $docType, $category);
                if ($info) {
                    $dataset[] = $info['data'];
                }
            }

            if (empty($dataset)) return back()->with('error', 'Gagal memproses data.');
            if ($docType == 'lampiran_d') {
                $viewName = 'pdf.bulk_lampiran_d';
            } elseif ($docType == 'submission_form') {
                $viewName = 'pdf.bulk_bg_confirmation';
            } else {
                return back()->with('error', 'Fitur Merge dokumen ini belum tersedia.');
            }

            // Untuk mode Merged, dataset sudah disiapkan di atas
            $pdf = Pdf::loadView($viewName, ['dataset' => $dataset]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream($baseFileName . '.pdf');
        } else {
            // Mode ZIP (File Terpisah)
            $zipName = $baseFileName . '.zip';
            $zipPath = storage_path('app/public/temp_zip/' . $zipName);

            if (!file_exists(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($ids as $id) {
                    $info = $this->prepareViewData($id, $docType, $category);
                    if ($info) {
                        // --- PERBAIKAN: Bungkus dataset jika view adalah bg_confirmation ---
                        $viewData = $info['data'];
                        if ($info['view'] === 'pdf.bg_confirmation') {
                            $viewData = ['dataset' => [$info['data']]];
                        }

                        $pdfContent = Pdf::loadView($info['view'], $viewData)
                                        ->setPaper('A4', 'portrait')
                                        ->output();
                        $zip->addFromString($info['filename'], $pdfContent);
                    }
                }
                $zip->close();
            }
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }
    }

    public function downloadDoc($id, $doc_type)
    {
        $info = $this->prepareViewData($id, $doc_type, 'transactions');

        if(!$info) return abort(404, 'Data tidak ditemukan.');

        // --- PERBAIKAN PENTING DI SINI ---
        // Jika view yang dipanggil adalah bg_confirmation,
        // kita harus membungkus datanya dalam array 'dataset'
        // karena view tersebut menggunakan foreach($dataset as $data).

        $viewData = $info['data'];

        if ($info['view'] === 'pdf.bg_confirmation') {
            $viewData = ['dataset' => [$info['data']]];
        }

        $pdf = Pdf::loadView($info['view'], $viewData);
        return $pdf->stream($info['filename']);
    }

    public function downloadLetters($id, $letter_type)
    {
        $info = $this->prepareViewData($id, $letter_type, 'expiring');

        if(!$info) return abort(404, 'Data tidak ditemukan.');

        $pdf = Pdf::loadView($info['view'], $info['data']);
        return $pdf->stream($info['filename']);
    }
}
