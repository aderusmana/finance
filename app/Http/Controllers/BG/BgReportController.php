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
                        return '<div class="action-btn-group">
                                    <button class="btn btn-info action-btn-hover btn-print-modal" data-id="'.$row->id.'" data-category="transactions" data-tooltip="Print Document">
                                        <i class="ph-bold ph-printer text-white"></i>
                                    </button>
                                </div>';
                    })->rawColumns(['checkbox', 'form_code', 'status', 'action'])
                    ->make(true);
            }

            // --- TAB 2: EXPIRING LETTERS ---
            if ($request->type == 'expiring') {
                $query = BankGaransi::with(['customer', 'details'])->where('status', '!=', 'returned')->orderBy('exp_date', 'asc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function($row) {
                        return '<div class="form-check text-center"><input class="form-check-input dt-checkbox" type="checkbox" value="'.$row->id.'"></div>';
                    })
                    ->addColumn('bg_number', function($row) {
                        $firstDetail = $row->details->first();
                        $bankName = $firstDetail->bank_name ?? '';
                        $bankBadge = $bankName ? ' <span class="badge bg-light text-primary border ms-1 fw-semibold"><i class="ph-bold ph-bank me-1"></i>'.$bankName.'</span>' : '';
                        return '<span class="text-primary fw-bold">'.$row->bg_number.'</span>' . $bankBadge;
                    })
                    ->addColumn('customer', fn($row) => $row->customer->name ?? '-')
                    ->addColumn('exp_date', fn($row) => $row->exp_date ? date('d M Y', strtotime($row->exp_date)) : '-')
                    ->addColumn('nominal', fn($row) => 'Rp ' . number_format($row->bg_nominal, 0, ',', '.'))
                    ->addColumn('action', function($row) {
                        return '<div class="action-btn-group">
                                    <button class="btn btn-info action-btn-hover btn-print-modal" data-id="'.$row->id.'" data-category="expiring" data-tooltip="Print Letters">
                                        <i class="ph-bold ph-envelope-open text-white"></i>
                                    </button>
                                </div>';
                    })->rawColumns(['checkbox', 'bg_number', 'action'])
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
            $bg = BankGaransi::with(['customer', 'details'])->find($id);
            if (!$bg) return null;

            $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $bg->customer->name, $bg->created_at ?? now());
            $firstDetail = $bg->details->first();
            $bankName = $firstDetail->bank_name ?? 'Bank Terkait';
            $branchName = $firstDetail->branch_name ?? '';
            $nominal = ($firstDetail && $firstDetail->nominal > 0) ? $firstDetail->nominal : $bg->bg_nominal;
            $expDate = $bg->exp_date ? \Carbon\Carbon::parse($bg->exp_date)->toDateString() : now()->addYear()->toDateString();

            $data = [
                'customer' => $bg->customer,
                'bg' => $bg,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $expDate,
                'bank_name' => $bankName,
                'branch_name' => $branchName,
                'nominal' => $nominal,
                'finance_name' => $financeName,
                'sales_name' => $salesName
            ];

            $cleanCust = \Illuminate\Support\Str::slug($bg->customer->name ?? 'customer', '_');
            $cleanBank = \Illuminate\Support\Str::slug($bankName, '_');
            $cleanBgNum = \Illuminate\Support\Str::slug($bg->bg_number ?? "id-{$bg->id}", '_');

            if ($doc_type == 'distributor') {
                return [
                    'view' => 'pdf.surat_distributor',
                    'data' => $data,
                    'filename' => 'Surat_Distributor_' . $cleanCust . '_' . $cleanBank . '_' . $cleanBgNum . '.pdf'
                ];
            } else {
                return [
                    'view' => 'pdf.surat_bank',
                    'data' => $data,
                    'filename' => 'Surat_Bank_' . $cleanBank . '_' . $cleanBgNum . '.pdf'
                ];
            }
        }
        return null;
    }

    public function bulkDownload(Request $request)
    {
        $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
        $docType = $request->doc_type;
        $category = $request->category;
        $outputMode = $request->output_mode;
        if (empty($ids)) return back()->with('error', 'No data selected.');
        $baseFileName = 'Bulk_' . ucfirst($docType) . '_' . date('Ymd_His');

        if ($outputMode == 'merged') {
            $dataset = [];

            if ($category === 'expiring') {
                foreach ($ids as $id) {
                    $bg = BankGaransi::with(['customer', 'details'])->find($id);
                    if (!$bg) continue;

                    $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $bg->customer->name, $bg->created_at ?? now());
                    $firstDetail = $bg->details->first();
                    $bankName = $firstDetail->bank_name ?? 'Bank Terkait';
                    $branchName = $firstDetail->branch_name ?? '';
                    $nominal = ($firstDetail && $firstDetail->nominal > 0) ? $firstDetail->nominal : $bg->bg_nominal;
                    $expDate = $bg->exp_date ? \Carbon\Carbon::parse($bg->exp_date)->toDateString() : now()->addYear()->toDateString();

                    $financeUser = User::role('manager-finance')->first();
                    $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head';
                    $salesUser = User::role('head-SNM')->first();
                    $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

                    $dataset[] = [
                        'customer' => $bg->customer,
                        'bg' => $bg,
                        'nomor_pkd' => $nomorPkd,
                        'expired_date' => $expDate,
                        'bank_name' => $bankName,
                        'branch_name' => $branchName,
                        'nominal' => $nominal,
                        'finance_name' => $financeName,
                        'sales_name' => $salesName
                    ];
                }

                if (empty($dataset)) return back()->with('error', 'Failed to process data.');

                if ($docType == 'all_letters') {
                    $viewName = 'pdf.bulk_all_letters';
                } elseif ($docType == 'bank') {
                    $viewName = 'pdf.bulk_surat_bank';
                } else {
                    $viewName = 'pdf.bulk_surat_distributor';
                }

                $pdf = Pdf::loadView($viewName, ['dataset' => $dataset]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream($baseFileName . '.pdf');
            }

            foreach ($ids as $id) {
                $info = $this->prepareViewData($id, $docType, $category);
                if ($info) {
                    $dataset[] = $info['data'];
                }
            }

            if (empty($dataset)) return back()->with('error', 'Failed to process data.');
            if ($docType == 'lampiran_d') {
                $viewName = 'pdf.bulk_lampiran_d';
            } elseif ($docType == 'submission_form') {
                $viewName = 'pdf.bulk_bg_confirmation';
            } else {
                return back()->with('error', 'The Merge feature for this document is not yet available.');
            }

            $pdf = Pdf::loadView($viewName, ['dataset' => $dataset]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream($baseFileName . '.pdf');
        } else {
            // Mode ZIP (File Terpisah per file)
            $zipName = $baseFileName . '.zip';
            $zipPath = storage_path('app/public/temp_zip/' . $zipName);

            if (!file_exists(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                if ($category === 'expiring') {
                    $financeUser = User::role('manager-finance')->first();
                    $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head';
                    $salesUser = User::role('head-SNM')->first();
                    $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

                    foreach ($ids as $id) {
                        $bg = BankGaransi::with(['customer', 'details'])->find($id);
                        if (!$bg) continue;

                        $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $bg->customer->name, $bg->created_at ?? now());
                        $firstDetail = $bg->details->first();
                        $bankName = $firstDetail->bank_name ?? 'Bank';
                        $branchName = $firstDetail->branch_name ?? '';
                        $nominal = ($firstDetail && $firstDetail->nominal > 0) ? $firstDetail->nominal : $bg->bg_nominal;
                        $expDate = $bg->exp_date ? \Carbon\Carbon::parse($bg->exp_date)->toDateString() : now()->addYear()->toDateString();

                        $cleanCust = \Illuminate\Support\Str::slug($bg->customer->name ?? 'customer', '_');
                        $cleanBank = \Illuminate\Support\Str::slug($bankName, '_');
                        $cleanBgNum = \Illuminate\Support\Str::slug($bg->bg_number ?? "id-{$bg->id}", '_');

                        $data = [
                            'customer' => $bg->customer,
                            'bg' => $bg,
                            'nomor_pkd' => $nomorPkd,
                            'expired_date' => $expDate,
                            'bank_name' => $bankName,
                            'branch_name' => $branchName,
                            'nominal' => $nominal,
                            'finance_name' => $financeName,
                            'sales_name' => $salesName
                        ];

                        if ($docType == 'all_letters' || $docType == 'all') {
                            // 1. Surat Bank
                            $bankPdf = Pdf::loadView('pdf.surat_bank', $data)->setPaper('A4', 'portrait')->output();
                            $zip->addFromString("Surat_Bank_{$cleanBank}_{$cleanBgNum}.pdf", $bankPdf);

                            // 2. Surat Distributor
                            $distPdf = Pdf::loadView('pdf.surat_distributor', $data)->setPaper('A4', 'portrait')->output();
                            $zip->addFromString("Surat_Distributor_{$cleanCust}_{$cleanBank}_{$cleanBgNum}.pdf", $distPdf);
                        } elseif ($docType == 'bank') {
                            $bankPdf = Pdf::loadView('pdf.surat_bank', $data)->setPaper('A4', 'portrait')->output();
                            $zip->addFromString("Surat_Bank_{$cleanBank}_{$cleanBgNum}.pdf", $bankPdf);
                        } elseif ($docType == 'distributor') {
                            $distPdf = Pdf::loadView('pdf.surat_distributor', $data)->setPaper('A4', 'portrait')->output();
                            $zip->addFromString("Surat_Distributor_{$cleanCust}_{$cleanBank}_{$cleanBgNum}.pdf", $distPdf);
                        }
                    }
                } else {
                    foreach ($ids as $id) {
                        $info = $this->prepareViewData($id, $docType, $category);
                        if ($info) {
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
                }
                $zip->close();
            }
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }
    }

    public function downloadDoc($id, $doc_type)
    {
        $info = $this->prepareViewData($id, $doc_type, 'transactions');

        if(!$info) return abort(404, 'Data not found.');

        $viewData = $info['data'];

        if ($info['view'] === 'pdf.bg_confirmation') {
            $viewData = ['dataset' => [$info['data']]];
        }

        $pdf = Pdf::loadView($info['view'], $viewData);
        return $pdf->stream($info['filename']);
    }

    public function downloadLetters($id, $letter_type)
    {
        if ($letter_type === 'all' || $letter_type === 'all_letters') {
            $bg = BankGaransi::with(['customer', 'details'])->findOrFail($id);
            $financeUser = User::role('manager-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head';
            $salesUser = User::role('head-SNM')->first();
            $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

            $firstDetail = $bg->details->first();
            $bankName = $firstDetail->bank_name ?? 'Bank';
            $branchName = $firstDetail->branch_name ?? '';
            $nominal = ($firstDetail && $firstDetail->nominal > 0) ? $firstDetail->nominal : $bg->bg_nominal;
            $expDate = $bg->exp_date ? \Carbon\Carbon::parse($bg->exp_date)->toDateString() : now()->addYear()->toDateString();
            $nomorPkd = DocumentHelper::generatePKDNumber($bg->id, $bg->customer->name, $bg->created_at ?? now());

            $cleanBank = \Illuminate\Support\Str::slug($bankName, '_');
            $cleanBgNum = \Illuminate\Support\Str::slug($bg->bg_number ?? "id-{$bg->id}", '_');
            $cleanCust = \Illuminate\Support\Str::slug($bg->customer->name ?? 'customer', '_');

            $zipName = "Surat_Lengkap_{$cleanBank}_{$cleanBgNum}.zip";
            $zipPath = storage_path('app/public/temp_zip/' . $zipName);
            if (!file_exists(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // 1. Surat Bank
                $bankPdf = Pdf::loadView('pdf.surat_bank', [
                    'customer' => $bg->customer, 'bg' => $bg, 'nomor_pkd' => $nomorPkd,
                    'expired_date' => $expDate, 'bank_name' => $bankName, 'branch_name' => $branchName,
                    'nominal' => $nominal, 'finance_name' => $financeName, 'sales_name' => $salesName
                ])->setPaper('A4', 'portrait')->output();
                $zip->addFromString("Surat_Bank_{$cleanBank}_{$cleanBgNum}.pdf", $bankPdf);

                // 2. Surat Distributor
                $distPdf = Pdf::loadView('pdf.surat_distributor', [
                    'customer' => $bg->customer, 'bg' => $bg, 'nomor_pkd' => $nomorPkd,
                    'expired_date' => $expDate, 'nominal' => $nominal,
                    'finance_name' => $financeName, 'sales_name' => $salesName
                ])->setPaper('A4', 'portrait')->output();
                $zip->addFromString("Surat_Distributor_{$cleanCust}_{$cleanBank}_{$cleanBgNum}.pdf", $distPdf);

                $zip->close();
            }
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }

        $info = $this->prepareViewData($id, $letter_type, 'expiring');

        if(!$info) return abort(404, 'Data not found.');

        $pdf = Pdf::loadView($info['view'], $info['data']);
        return $pdf->stream($info['filename']);
    }

    public function downloadPackage($id, Request $request)
    {
        $submission = BgSubmission::with(['recommendation.customer', 'recommendation.periods'])->findOrFail($id);
        $rec = $submission->recommendation;
        $customer = $rec ? $rec->customer : null;

        if (!$customer) {
            return back()->with('error', 'Data customer untuk pengajuan ini tidak ditemukan.');
        }

        $financeUser = User::role('manager-finance')->first();
        $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head';
        $salesUser = User::role('head-SNM')->first();
        $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head';

        $nomorPkd = $customer->no_pkd;
        if (empty($nomorPkd)) {
            $nomorPkd = DocumentHelper::generatePKDNumber($rec ? $rec->id : $submission->id, $customer->name, $submission->created_at);
        }

        // Cari kandidat Bank Garansi terkait submission ini
        $candidateBgs = BankGaransi::where('customer_id', $customer->id)
            ->whereBetween('created_at', [
                $submission->created_at->copy()->subMinutes(10),
                $submission->created_at->copy()->addMinutes(10)
            ])
            ->orderBy('id', 'asc')
            ->with('details')
            ->get();

        if ($candidateBgs->isEmpty()) {
            $candidateBgs = BankGaransi::where('customer_id', $customer->id)
                ->latest()
                ->take(3)
                ->with('details')
                ->get();
        }

        $only = $request->query('only');
        if ($only === 'distributor') {
            $totalDistNominal = $candidateBgs->sum('bg_nominal') ?: ($submission->bg_nominal ?: 0);
            $expDateDist = $candidateBgs->first() && $candidateBgs->first()->exp_date
                ? \Carbon\Carbon::parse($candidateBgs->first()->exp_date)->toDateString()
                : ($submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->toDateString() : now()->addYear()->toDateString());

            $cleanCust = \Illuminate\Support\Str::slug($customer->name, '_');
            return Pdf::loadView('pdf.surat_distributor', [
                'customer' => $customer,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $expDateDist,
                'nominal' => $totalDistNominal,
                'finance_name' => $financeName,
                'sales_name' => $salesName,
            ])->setPaper('A4', 'portrait')->stream("Surat_Distributor_{$cleanCust}.pdf");
        }

        if ($only === 'bank' && $candidateBgs->count() === 1) {
            $bg = $candidateBgs->first();
            $firstDetail = $bg->details->first();
            $bankName = $firstDetail->bank_name ?? 'Bank Terkait';
            $branchName = $firstDetail->branch_name ?? '';
            $nominal = ($firstDetail && $firstDetail->nominal > 0) ? $firstDetail->nominal : $bg->bg_nominal;
            $expDate = $bg->exp_date ? \Carbon\Carbon::parse($bg->exp_date)->toDateString() : ($submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->toDateString() : now()->addYear()->toDateString());

            $cleanBank = \Illuminate\Support\Str::slug($bankName, '_');
            return Pdf::loadView('pdf.surat_bank', [
                'customer' => $customer,
                'bg' => $bg,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $expDate,
                'bank_name' => $bankName,
                'branch_name' => $branchName,
                'nominal' => $nominal,
                'finance_name' => $financeName,
                'sales_name' => $salesName,
            ])->setPaper('A4', 'portrait')->stream("Surat_Bank_{$cleanBank}.pdf");
        }

        // FULL PACKAGE (ZIP berisi file-file terpisah: Lampiran D, Surat Bank untuk tiap bank, Surat Distributor)
        $submissionDates = BgSubmission::where('bg_recommendation_id', $rec ? $rec->id : 0)->pluck('created_at');
        $totalBgDiserahkan = BankGaransi::where('customer_id', $customer->id)
                                ->whereIn('created_at', $submissionDates)
                                ->sum('bg_nominal');

        if ($totalBgDiserahkan == 0) {
            $lastBg = BankGaransi::where('customer_id', $customer->id)
                    ->where('status', '!=', 'draft')
                    ->latest()->first();
            $totalBgDiserahkan = $lastBg ? $lastBg->bg_nominal : 0;
        }

        $lampiranData = [
            'submission' => $submission,
            'rec' => $rec,
            'customer' => $customer,
            'nomor_pkd' => $nomorPkd,
            'total_bg_diserahkan' => $totalBgDiserahkan,
            'finance_name' => $financeName,
            'sales_name' => $salesName,
            'bg' => $candidateBgs->first()
        ];

        $cleanCust = \Illuminate\Support\Str::slug($customer->name, '_');
        $cleanCode = \Illuminate\Support\Str::slug($submission->form_code, '_');
        $zipName = "Berkas_Bank_{$cleanCust}_{$cleanCode}.zip";
        $zipPath = storage_path('app/public/temp_zip/' . $zipName);
        if (!file_exists(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // 1. Lampiran D
            $lampiranPdf = Pdf::loadView('pdf.lampiran_d', $lampiranData)->setPaper('A4', 'portrait')->output();
            $zip->addFromString("01_Lampiran_D_{$cleanCode}.pdf", $lampiranPdf);

            // 2. Surat Bank (Dibuat untuk SETIAP bank jika ada multi-bank)
            if ($candidateBgs->isNotEmpty()) {
                $idx = 1;
                foreach ($candidateBgs as $bg) {
                    $details = $bg->details->isNotEmpty() ? $bg->details : [null];
                    foreach ($details as $detail) {
                        $bankName = $detail->bank_name ?? 'Bank';
                        $branchName = $detail->branch_name ?? '';
                        $nominal = ($detail && $detail->nominal > 0) ? $detail->nominal : $bg->bg_nominal;
                        $expDate = $bg->exp_date ? \Carbon\Carbon::parse($bg->exp_date)->toDateString() : ($submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->toDateString() : now()->addYear()->toDateString());

                        $bankPdf = Pdf::loadView('pdf.surat_bank', [
                            'customer' => $customer,
                            'bg' => $bg,
                            'nomor_pkd' => $nomorPkd,
                            'expired_date' => $expDate,
                            'bank_name' => $bankName,
                            'branch_name' => $branchName,
                            'nominal' => $nominal,
                            'finance_name' => $financeName,
                            'sales_name' => $salesName,
                        ])->setPaper('A4', 'portrait')->output();

                        $cleanBank = \Illuminate\Support\Str::slug($bankName, '_');
                        $cleanBgNum = \Illuminate\Support\Str::slug($bg->bg_number ?? "warkat-{$idx}", '_');
                        $zip->addFromString("02_Surat_Bank_{$cleanBank}_{$cleanBgNum}.pdf", $bankPdf);
                        $idx++;
                    }
                }
            } else {
                $expDate = $submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->toDateString() : now()->addYear()->toDateString();
                $bankPdf = Pdf::loadView('pdf.surat_bank', [
                    'customer' => $customer,
                    'bg' => null,
                    'nomor_pkd' => $nomorPkd,
                    'expired_date' => $expDate,
                    'bank_name' => 'Bank Terkait',
                    'branch_name' => '',
                    'nominal' => $submission->bg_nominal ?: $totalBgDiserahkan,
                    'finance_name' => $financeName,
                    'sales_name' => $salesName,
                ])->setPaper('A4', 'portrait')->output();
                $zip->addFromString("02_Surat_Bank_Pengantar.pdf", $bankPdf);
            }

            // 3. Surat Distributor
            $totalDistNominal = $candidateBgs->sum('bg_nominal') ?: ($submission->bg_nominal ?: $totalBgDiserahkan);
            $expDateDist = $candidateBgs->first() && $candidateBgs->first()->exp_date
                ? \Carbon\Carbon::parse($candidateBgs->first()->exp_date)->toDateString()
                : ($submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->toDateString() : now()->addYear()->toDateString());

            $distPdf = Pdf::loadView('pdf.surat_distributor', [
                'customer' => $customer,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $expDateDist,
                'nominal' => $totalDistNominal,
                'finance_name' => $financeName,
                'sales_name' => $salesName,
            ])->setPaper('A4', 'portrait')->output();
            $zip->addFromString("03_Surat_Distributor_{$cleanCust}.pdf", $distPdf);

            $zip->close();
        }

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}
