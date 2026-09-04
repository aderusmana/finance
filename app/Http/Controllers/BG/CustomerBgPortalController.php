<?php

namespace App\Http\Controllers\BG;

use App\Helpers\DocumentHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgSubmission;
use Illuminate\Support\Facades\DB;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BgSubmissionDocumentMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Mail\BgUpdateDocumentMail;
use App\Mail\CustomerFillFormNotification;
use App\Models\Master\ApprovalLog;
use App\Jobs\ProcessFinanceApprovalEmail;
use Illuminate\Support\Facades\Log;

class CustomerBgPortalController extends Controller
{
    public function showInputForm($token)
    {
        $rec = BgRecommendation::with('customer')->where('token', $token)->first();

        if (!$rec || $rec->status !== 'process') {
            return view('page.customer_portal.expired_or_completed');
        }

        $metadata = json_decode($rec->notes, true);
        $action = $metadata['action'] ?? 'new';
        $existingBg = null;

        if ($action === 'existing' && !empty($metadata['target_bg_id'])) {
            $existingBg = BankGaransi::with('details')->find($metadata['target_bg_id']);
        }

        return view('page.customer_portal.form-input-bank', compact('rec', 'token', 'action', 'existingBg'));
    }

    public function storeInputData(Request $request, $token)
    {
        $rec = BgRecommendation::with('customer')->where('token', $token)->firstOrFail();

        if ($rec->status != 'process') {
            return view('page.customer_portal.expired_or_completed');
        }

        $metadata = json_decode($rec->notes, true);
        $action = $metadata['action'] ?? 'new';
        $financeUser = User::role('head-finance')->first();
        $financeName = $financeUser ? $financeUser->name : 'Finance Dept.';

        $request->validate([
            'custom_address'      => 'nullable|string|max:1000',
            'details'             => 'required|array',
            'details.*.nominal'   => 'required|numeric',
            'details.*.bank_name' => ($action === 'existing') ? 'nullable' : 'required',
        ]);

        DB::beginTransaction();
        try {
            $submission = null;
            $timestamp = now();
            $msgType = '';

            if ($action === 'existing' && !empty($metadata['target_bg_id'])) {
                $msgType = 'Update Existing Data';
                $bg = BankGaransi::findOrFail($metadata['target_bg_id']);
                $oldNominal = $bg->bg_nominal;
                $newNominal = (float) $request->details[0]['nominal'];

                $bg->update([
                    'bg_nominal' => $newNominal,
                    'updated_at' => $timestamp
                ]);

                $bg->details()->update(['nominal' => $newNominal]);
                $formCode = 'UPD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $submission = BgSubmission::create([
                    'bg_recommendation_id' => $rec->id,
                    'form_code'            => $formCode,
                    'custom_address'       => $request->custom_address,
                    'status'               => 'awaiting_upload',
                    'token'                => Str::random(60),
                    'created_at'           => $timestamp
                ]);

                activity()
                    ->causedBy($rec->customer)
                    ->performedOn($bg)
                    ->useLog('bg_transaction')
                    ->event('update_nominal')
                    ->withProperties([
                        'bg_number'   => $bg->bg_number,
                        'old_nominal' => $oldNominal,
                        'new_nominal' => $newNominal,
                        'difference'  => $newNominal - $oldNominal,
                        'form_code'   => $submission->form_code
                    ])
                    ->log("Customer performed EXISTING update: Nominal changed from Rp " . number_format($oldNominal) . " to Rp " . number_format($newNominal));

                $dataset = [[
                    'bg'           => $bg,
                    'bgs'          => [$bg],
                    'customer'     => $rec->customer,
                    'submission'   => $submission,
                    'rec'          => $rec,
                    'finance_name' => $financeName,
                    'is_existing'  => true,
                    'old_nominal'  => $oldNominal
                ]];

                $pdf = Pdf::loadView('pdf.bg_confirmation', ['dataset' => $dataset]);
                $fileName = 'Formulir_Update_' . $submission->form_code . '.pdf';
                Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

                $custEmail = $rec->customer->email ?? null;
                if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($custEmail)
                        ->queue(new BgUpdateDocumentMail($submission, base64_encode($pdf->output()), 'existing'));
                }
            } else {
                $msgType = ($action === 'extension') ? 'Input Extension BG' : 'Input New BG';
                $currentYear = date('Y');
                $existingCount = BankGaransi::where('customer_id', $rec->customer_id)
                                    ->whereYear('created_at', $currentYear)
                                    ->count();

                // 1 Single Submission for the entire batch of banks
                $formCode = ($action === 'extension' ? 'EXT-' : 'NEW-') . date('Ymd') . '-' . strtoupper(Str::random(6));
                $submission = BgSubmission::create([
                    'bg_recommendation_id' => $rec->id,
                    'form_code'            => $formCode,
                    'custom_address'       => $request->custom_address,
                    'status'               => 'awaiting_upload',
                    'token'                => Str::random(60),
                    'created_at'           => $timestamp,
                ]);

                $createdBgs = [];

                foreach ($request->details as $index => $d) {
                    $nominal = (float) $d['nominal'];
                    $sequence = $existingCount + ($index + 1);
                    $bgNumber = "BG-{$currentYear}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    $bg = BankGaransi::create([
                        'customer_id' => $rec->customer_id,
                        'bg_number'   => $bgNumber,
                        'bg_type'     => ($action === 'extension') ? 'extension' : 'new',
                        'bg_nominal'  => $nominal,
                        'base_bg_id'  => null,
                        'status'      => 'draft',
                        'created_by'  => $rec->customer->user_id ?? null,
                        'created_at'  => $timestamp,
                    ]);
                    $bg->update(['base_bg_id' => $bg->id]);

                    $logMessage = ($action === 'extension')
                        ? "Customer submitted New EXTENSION BG for Rp " . number_format($nominal)
                        : "Customer submitted NEW BG for Rp " . number_format($nominal);

                    activity()
                        ->causedBy($rec->customer)
                        ->performedOn($bg)
                        ->useLog('bg_transaction')
                        ->event($action === 'extension' ? 'create_extension' : 'create_new')
                        ->withProperties([
                            'bg_number' => $bg->bg_number,
                            'nominal'   => $nominal,
                            'bank'      => $d['bank_name']
                        ])
                        ->log($logMessage);

                    $bg->details()->create([
                        'bank_name'      => $d['bank_name'],
                        'branch_name'    => $d['branch_name'] ?? null,
                        'bank_address'   => $d['bank_address'] ?? null,
                        'contact_person' => $d['contact_person'] ?? null,
                        'nominal'        => $nominal,
                    ]);

                    $createdBgs[] = $bg->load('details');
                }

                // 1 Consolidated Dataset for PDF
                $dataset = [
                    [
                        'bg'           => $createdBgs[0] ?? null,
                        'bgs'          => $createdBgs,
                        'customer'     => $rec->customer,
                        'submission'   => $submission,
                        'rec'          => $rec,
                        'finance_name' => $financeName,
                        'is_existing'  => false,
                    ]
                ];

                $pdf = Pdf::loadView('pdf.bg_confirmation', ['dataset' => $dataset]);
                $fileName = 'Formulir_BG_' . $submission->form_code . '.pdf';
                Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

                $custEmail = $rec->customer->email ?? null;
                if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                    if ($action === 'extension') {
                        Mail::to($custEmail)
                            ->queue(new BgUpdateDocumentMail($submission, base64_encode($pdf->output()), 'extension'));
                    } else {
                        Mail::to($custEmail)
                            ->queue(new BgSubmissionDocumentMail($submission, base64_encode($pdf->output())));
                    }
                }
            }

            $rec->update(['status' => 'waiting_upload', 'token' => null]);

            DB::commit();

            try {
                $admins = User::role(['admin-rtm'])->get();

                Notification::send($admins, new SystemNotification(
                    'Customer Input Data',
                    "Customer <b>{$rec->customer->name}</b> has completed {$msgType} & 1 Combined Form Generated ({$submission->form_code}).",
                    route('bg-submissions.index'),
                    'ph-file-text',
                    'info'
                ));
            } catch (\Exception $e) {
                \Log::error('Notif Admin Error: ' . $e->getMessage());
            }

            $downloadUrl = route('customer.portal.download-pdf', ['token' => $submission->token]);

            return view('page.customer_portal.form-success', [
                'type'        => 'input_multi',
                'downloadUrl' => $downloadUrl,
                'uploadToken' => $submission->token,
                'message'     => 'Success! Document has been processed. Please download the single consolidated document, sign it, and upload it back along with the Bank Garansi scan.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'System error occurred: ' . $e->getMessage());
        }
    }

    public function downloadPdf($token)
    {
        try {
            $submission = BgSubmission::with('recommendation.customer')->where('token', $token)->firstOrFail();
            $prefix = str_starts_with($submission->form_code, 'UPD') ? 'Formulir_Update_' : 'Formulir_BG_';
            $fileName = $prefix . str_replace(['/', '\\'], '-', $submission->form_code) . '.pdf';
            $path = 'generated_pdfs/' . $fileName;

            if (Storage::disk('public')->exists($path)) {
                return response()->download(storage_path('app/public/' . $path), $fileName);
            }

            $rec = $submission->recommendation;
            $metadata = json_decode($rec->notes, true) ?? [];
            $action = $metadata['action'] ?? 'new';

            if ($action === 'existing' && !empty($metadata['target_bg_id'])) {
                $bg = BankGaransi::with('details')->find($metadata['target_bg_id']);

                $dataset = [
                    [
                        'bg'          => $bg,
                        'bgs'         => [$bg],
                        'customer'    => $rec->customer,
                        'submission'  => $submission,
                        'rec'         => $rec,
                        'is_existing' => true,
                        'old_nominal' => $bg ? $bg->bg_nominal : 0
                    ]
                ];
            } else {
                $createdAt = Carbon::parse($submission->created_at);
                $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                                ->whereBetween('created_at', [
                                    $createdAt->copy()->subMinutes(2),
                                    $createdAt->copy()->addMinutes(2)
                                ])
                                ->with('details')
                                ->orderBy('id', 'asc')
                                ->get();

                if ($candidateBgs->isEmpty()) {
                    $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                                    ->with('details')
                                    ->latest()
                                    ->take(5)
                                    ->get();
                }

                $dataset = [
                    [
                        'bg'          => $candidateBgs->first(),
                        'bgs'         => $candidateBgs,
                        'customer'    => $rec->customer,
                        'submission'  => $submission,
                        'rec'         => $rec,
                        'is_existing' => false,
                    ]
                ];
            }

            $pdf = Pdf::loadView('pdf.bg_confirmation', ['dataset' => $dataset]);
            Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            abort(404, 'Document file not found or system error occurred.');
        }
    }

    public function showUploadForm($token)
    {
        $submission = BgSubmission::where('token', $token)
                        ->where('status', 'awaiting_upload')
                        ->with('recommendation.customer')
                        ->first();

        if (!$submission) {
             return view('page.customer_portal.form-invalid');
        }

        $rec = $submission->recommendation;
        $metadata = json_decode($rec->notes, true);
        $action = $metadata['action'] ?? 'new';

        if ($action === 'existing') {
            $bg = null;

            if (isset($metadata['target_bg_id'])) {
                $bg = BankGaransi::with('details')->find($metadata['target_bg_id']);
            } else {
                $bg = BankGaransi::where('customer_id', $rec->customer_id)
                        ->where('created_at', $submission->created_at)
                        ->with('details')
                        ->first();
            }

            return view('page.customer_portal.update_upload_form', [
                'submission' => $submission,
                'token'      => $token,
                'bg'         => $bg,
                'type'       => $action
            ]);
        }

        $createdAt = Carbon::parse($submission->created_at);
        $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                            ->whereBetween('created_at', [
                                $createdAt->copy()->subMinutes(5),
                                $createdAt->copy()->addMinutes(5)
                            ])
                            ->with('details')
                            ->orderBy('id', 'asc')
                            ->get();

        if ($candidateBgs->isEmpty()) {
            $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                            ->where('status', 'draft')
                            ->with('details')
                            ->latest()
                            ->get();
        }

        if ($candidateBgs->isEmpty()) {
            $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                            ->with('details')
                            ->latest()
                            ->take(5)
                            ->get();
        }

        $bg = $candidateBgs->first();
        $bgs = $candidateBgs;

        return view('page.customer_portal.upload_form', compact('submission', 'token', 'bg', 'bgs'));
    }

    public function storeUploadData(Request $request, $token)
    {
        Log::info("Mencoba upload dokumen dengan token: " . $token);
        $submission = BgSubmission::where('token', $token)->first();

        if (!$submission) {
            Log::error("Token tidak ditemukan: " . $token);
            return back()->with('error', 'Token is expired or invalid.');
        }

        if ($submission->status != 'awaiting_upload') {
            Log::warning("Status submission bukan awaiting_upload: " . $submission->status);
            return back()->with('error', 'Document has already been uploaded.');
        }

        $request->validate([
            'signed_document' => 'required|file|mimes:pdf|max:10240',
        ], [
            'signed_document.required' => 'Dokumen konfirmasi bertandatangan dan cap perusahaan wajib diunggah.',
            'signed_document.mimes'    => 'Format file dokumen konfirmasi harus berupa PDF.',
            'signed_document.max'      => 'Ukuran file dokumen konfirmasi maksimal 10MB.',
        ]);

        try {
            $signedFile = $request->file('signed_document');
            $signedPath = $signedFile->store('bg_documents/signed', 'public');

            $submission->update([
                'signed_document_path' => 'storage/' . $signedPath,
                'submitted_at'         => now(),
                'upload_completed_at'  => now(),
                'status'               => 'uploaded',
            ]);

            $rec = $submission->recommendation;

            activity()
                ->causedBy($rec->customer ?? null)
                ->performedOn($submission)
                ->log("Customer uploaded signed confirmation document ({$submission->form_code})");

            Log::info("Upload Dokumen Konfirmasi Berhasil untuk Submission ID: " . $submission->id);

            // Send instant notification and email to Admin RTM & Sales
            try {
                $recipients = User::role(['admin-rtm', 'sales', 'dep-SNM'])->get();

                Notification::sendNow($recipients, new SystemNotification(
                    'Customer Uploaded Confirmation Document',
                    "Customer <b>{$rec->customer->name}</b> telah mengunggah dokumen konfirmasi Bank Garansi ({$submission->form_code}). Menunggu pengisian nomor BG & scan dokumen Bank Garansi oleh tim Sales / Admin.",
                    route('bg-submissions.index'),
                    'ph-upload-simple',
                    'success'
                ));

                foreach ($recipients as $recipient) {
                    if ($recipient->email && filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($recipient->email)->send(new CustomerFillFormNotification($rec, true, $submission));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Notif Upload Sales/Admin Error: ' . $e->getMessage());
            }

            return redirect()->route('customer.portal.upload-success');

        } catch (\Exception $e) {
            Log::error("Error Exception saat upload: " . $e->getMessage());
            return back()->with('error', 'Upload failed (Server Error): ' . $e->getMessage());
        }
    }

    public function downloadExpiringPdf($bg_id, $type)
    {
        try {
            $bg = BankGaransi::with('customer')->findOrFail($bg_id);
            $cust = $bg->customer;
            $financeUser = User::role('head-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head Tidak Diketahui';
            $nomorPkd = DocumentHelper::generatePKDNumber($bg->temp_recommendation_id ?? $bg->id, $cust->name, now());

            $dataPdf = [
                'customer' => $cust,
                'bg' => $bg,
                'nomor_pkd' => $nomorPkd,
                'expired_date' => $bg->exp_date,
                'bank_name' => $bg->bank_name ?? 'Bank',
                'branch_name' => $bg->branch_name ?? '',
                'nominal' => $bg->bg_nominal,
                'finance_name' => $financeName
            ];

            $viewName = ($type === 'distributor') ? 'pdf.surat_distributor' : 'pdf.surat_bank';
            $fileName = ($type === 'distributor') ? 'Surat_Pemberitahuan_Distributor.pdf' : 'Surat_Pengantar_Bank.pdf';

            $pdf = Pdf::loadView($viewName, $dataPdf);

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            abort(404, 'Document not found or link expired.');
        }
    }

    public function downloadLampiranD($token)
    {
        try {
            $submission = BgSubmission::with(['recommendation.customer'])->where('token', $token)->first();

            if (!$submission) {
                return view('page.customer_portal.form-invalid');
            }

            $rec = $submission->recommendation;
            $customer = $rec->customer;

            $createdAt = Carbon::parse($submission->created_at);
            $startTime = $createdAt->copy()->subMinutes(2);
            $endTime   = $createdAt->copy()->addMinutes(2);

            $totalBgDiserahkan = BankGaransi::where('customer_id', $customer->id)
                                    ->whereBetween('created_at', [$startTime, $endTime])
                                    ->sum('bg_nominal');

            if ($totalBgDiserahkan == 0) {
                 $lastBgBatch = BankGaransi::where('customer_id', $customer->id)
                                    ->where('status', '!=', 'draft')
                                    ->latest()
                                    ->take(1)
                                    ->first();

                 if($lastBgBatch) {
                     $batchTime = Carbon::parse($lastBgBatch->created_at);
                     $totalBgDiserahkan = BankGaransi::where('customer_id', $customer->id)
                                            ->whereBetween('created_at', [
                                                $batchTime->copy()->subMinutes(2),
                                                $batchTime->copy()->addMinutes(2)
                                            ])->sum('bg_nominal');
                 }
            }

            $nomorPkd = $customer->no_pkd;
            if(empty($nomorPkd)) {
                 $nomorPkd = DocumentHelper::generatePKDNumber($rec->id, $customer->name, $customer->created_at);
            }

            $financeUser = User::role('head-finance')->first();
            $salesUser = User::role('head-SNM')->first();

            $data = [
                'submission' => $submission,
                'rec' => $rec,
                'customer' => $customer,
                'total_bg_diserahkan' => $totalBgDiserahkan,
                'nomor_pkd' => $nomorPkd,
                'sales_name' => $salesUser ? $salesUser->name : 'S&M Dept. Head',
                'finance_name' => $financeUser ? $financeUser->name : 'Manager Finance'
            ];

            $pdf = Pdf::loadView('pdf.lampiran_d', $data);
            $safeName = str_replace(['/', '\\'], '-', $customer->name);
            return $pdf->download('Lampiran_D_' . $safeName . '.pdf');

        } catch (\Exception $e) {
            abort(404, 'Document not found or error occurred: ' . $e->getMessage());
        }
    }

    public function reviewUpload($token)
    {
        $submission = BgSubmission::with('recommendation.customer')->where('token', $token)->first();
        if (!$submission || !$submission->signed_document_path) {
            return view('page.customer_portal.invalid');
        }

        return view('page.customer_portal.admin_review_upload', compact('submission', 'token'));
    }

    public function downloadSubmissionPdf($token)
    {
        try {
            $submission = BgSubmission::where('token', $token)->firstOrFail();
            $path = $submission->signed_document_path;

            if ($path && file_exists(public_path($path))) {
                $fileName = 'Uploaded_Document_' . $submission->form_code . '.pdf';
                return response()->download(public_path($path), $fileName);
            }

            return abort(404, 'File not found on server');
        } catch (\Exception $e) {
            abort(404, 'Document not found.');
        }
    }
}
