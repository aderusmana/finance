<?php

namespace App\Http\Controllers\BG;

use App\Helpers\DocumentHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgSubmission;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BgSubmissionDocumentMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Mail\BgUpdateDocumentMail;
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
        $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head Tidak Diketahui';

        $request->validate([
            'details' => 'required|array',
            'details.*.nominal' => 'required|numeric',
            'details.*.bank_name' => ($action === 'existing') ? 'nullable' : 'required',
        ]);

        DB::beginTransaction();
        try {
            $submission = null;
            $timestamp = now();

            if ($action === 'existing' && !empty($metadata['target_bg_id'])) {
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
                    'form_code'  => $formCode,
                    'status'     => 'awaiting_upload',
                    'token'      => Str::random(60),
                    'created_at' => $timestamp
                ]);

                $dataset = [
                    [
                        'bg' => $bg,
                        'customer' => $rec->customer,
                        'submission' => $submission,
                        'rec' => $rec,
                        'finance_name' => $financeName,
                        'is_existing' => true,
                        'old_nominal' => $oldNominal
                    ]
                ];

                $pdf = Pdf::loadView('pdf.bg_confirmation', ['dataset' => $dataset]);

                $fileName = 'Formulir_Update_' . $submission->form_code . '.pdf';
                Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

                if ($rec->customer && $rec->customer->email) {
                    Mail::to($rec->customer->email)
                        ->queue(new BgUpdateDocumentMail($submission, base64_encode($pdf->output()), 'existing'));
                }
            } else {
                $currentYear = date('Y');
                $existingCount = BankGaransi::where('customer_id', $rec->customer_id)
                                    ->whereYear('created_at', $currentYear)
                                    ->count();

                $financeUser = User::role('head-finance')->first();
                $financeName = $financeUser ? $financeUser->name : 'Finance Dept.';

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
                    ]);

                    $bg->update(['base_bg_id' => $bg->id]);

                    $bg->details()->create([
                        'bank_name'      => $d['bank_name'],
                        'branch_name'    => $d['branch_name'] ?? null,
                        'bank_address'   => $d['bank_address'] ?? null,
                        'contact_person' => $d['contact_person'] ?? null,
                        'nominal'        => $nominal,
                    ]);

                    $formCode = 'NEW-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . '-' . ($index+1);
                    $submission = BgSubmission::create([
                        'bg_recommendation_id' => $rec->id,
                        'form_code' => $formCode,
                        'status' => 'awaiting_upload',
                        'token'  => Str::random(60),
                    ]);

                    $datasetItem = [
                        'bg' => $bg,
                        'customer' => $rec->customer,
                        'submission' => $submission,
                        'rec' => $rec,
                        'finance_name' => $financeName
                    ];

                    $dataset = [$datasetItem];
                    $pdf = Pdf::loadView('pdf.bg_confirmation', ['dataset' => $dataset]);

                    $fileName = 'Formulir_BG_' . $submission->form_code . '.pdf';
                    Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

                    if ($rec->customer && $rec->customer->email) {
                        if($action === 'extension') {
                            Mail::to($rec->customer->email)
                                ->queue(new BgUpdateDocumentMail($submission, base64_encode($pdf->output()), 'extension'));
                        } else {
                            Mail::to($rec->customer->email)
                                ->queue(new BgSubmissionDocumentMail($submission, base64_encode($pdf->output())));
                        }
                    }
                }
                $submission = BgSubmission::where('bg_recommendation_id', $rec->id)->latest()->first();
            }

            $rec->update(['status' => 'waiting_upload', 'token' => null]);

            DB::commit();
            $downloadUrl = route('customer.portal.download-pdf', ['token' => $submission->token]);

            return view('page.customer_portal.form-success', [
                'type'        => 'input_multi',
                'downloadUrl' => $downloadUrl,
                'uploadToken' => $submission->token,
                'message'     => 'Berhasil! Dokumen telah diproses. Silakan cek email dan upload dokumen yang telah ditandatangani.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
                        'bg' => $bg,
                        'customer' => $rec->customer,
                        'submission' => $submission,
                        'rec' => $rec,
                        'is_existing' => true,
                        'old_nominal' => $bg->bg_nominal // Fallback: tampilkan nominal skrg jika history tdk dilacak disini
                    ]
                ];
            }
            else {
                $createdAt = Carbon::parse($submission->created_at);
                $startTime = $createdAt->copy()->subMinutes(5);
                $endTime   = $createdAt->copy()->addMinutes(5);

                $siblings = BgSubmission::where('bg_recommendation_id', $rec->id)
                            ->whereBetween('created_at', [$startTime, $endTime])
                            ->orderBy('id', 'asc')
                            ->pluck('id')->toArray();

                $myIndex = array_search($submission->id, $siblings);
                $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                                ->whereBetween('created_at', [$startTime, $endTime])
                                ->with('details')
                                ->orderBy('id', 'asc')
                                ->get();

                if ($myIndex !== false && isset($candidateBgs[$myIndex])) {
                    $bg = $candidateBgs[$myIndex];
                } else {
                    $bg = $candidateBgs->first();
                }

                $dataset = [
                    [
                        'bg' => $bg,
                        'customer' => $rec->customer,
                        'submission' => $submission,
                        'rec' => $rec
                    ]
                ];
            }

            $pdf = Pdf::loadView('pdf.bg_confirmation', ['dataset' => $dataset]);
            Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            abort(404, 'File dokumen tidak ditemukan atau terjadi kesalahan sistem.');
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

        if ($action === 'existing' || $action === 'extension') {
            $bg = null;

            if ($action === 'existing' && isset($metadata['target_bg_id'])) {
                $bg = BankGaransi::with('details')->find($metadata['target_bg_id']);
            } else {
                $bg = BankGaransi::where('customer_id', $rec->customer_id)
                        ->where('created_at', $submission->created_at)
                        ->with('details')
                        ->first();
            }

            return view('page.customer_portal.update_upload_form', [
                'submission' => $submission,
                'token' => $token,
                'bg' => $bg,
                'type' => $action
            ]);
        }

        $createdAt = Carbon::parse($submission->created_at);
        $startTime = $createdAt->copy()->subMinutes(5);
        $endTime   = $createdAt->copy()->addMinutes(5);
        $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $rec->id)
                                ->whereBetween('created_at', [$startTime, $endTime])
                                ->orderBy('id', 'asc')
                                ->pluck('id')
                                ->toArray();

        $myIndex = array_search($submission->id, $siblingSubmissions);
        $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                            ->whereBetween('created_at', [$startTime, $endTime])
                            ->with('details')
                            ->orderBy('id', 'asc')
                            ->get();

        $bg = null;
        if ($myIndex !== false && isset($candidateBgs[$myIndex])) {
            $bg = $candidateBgs[$myIndex];
        } else {
            $bg = $candidateBgs->first();
        }

        return view('page.customer_portal.upload_form', compact('submission', 'token', 'bg'));
    }

    public function storeUploadData(Request $request, $token)
    {
        Log::info("Mencoba upload dokumen dengan token: " . $token);
        $submission = BgSubmission::where('token', $token)->first();

        if (!$submission) {
            Log::error("Token tidak ditemukan: " . $token);
            return back()->with('error', 'Token kadaluarsa atau tidak valid.');
        }

        if ($submission->status != 'awaiting_upload') {
            Log::warning("Status submission bukan awaiting_upload: " . $submission->status);
            return back()->with('error', 'Dokumen sudah diupload sebelumnya.');
        }

        $request->validate([
            'signed_document' => 'required|mimes:pdf|max:5120', // Ubah 2048 jadi 5120
        ], [
            'signed_document.required' => 'File dokumen wajib diunggah.',
            'signed_document.mimes' => 'Format file harus PDF.',
            'signed_document.max' => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $file = $request->file('signed_document');
            Log::info("File diterima: " . $file->getClientOriginalName() . ", Size: " . $file->getSize());

            $path = $file->store('bg_documents/signed', 'public');
            $submission->update([
                'signed_document_path' => 'storage/' . $path,
                'submitted_at'         => now(),
                'upload_completed_at'  => now(),
                'status'               => 'uploaded',
                'token'                => null,
            ]);

            Log::info("Upload Berhasil untuk Submission ID: " . $submission->id);

            return view('page.customer_portal.form-success', [
                'type' => 'upload'
            ]);

        } catch (\Exception $e) {
            Log::error("Error Exception saat upload: " . $e->getMessage());
            return back()->with('error', 'Gagal upload (Server Error): ' . $e->getMessage());
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
            abort(404, 'Dokumen tidak ditemukan atau link kadaluarsa.');
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
            abort(404, 'Dokumen tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
