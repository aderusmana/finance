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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerBgPortalController extends Controller
{
    public function showInputForm($token)
    {
        $rec = BgRecommendation::with('customer')->where('token', $token)->first();

        if (!$rec) {
            return view('page.customer_portal.form-invalid');
        }

       if ($rec->status !== 'process') {
             return view('page.customer_portal.expired_or_completed');
        }

        return view('page.customer_portal.form-input-bank', compact('rec', 'token'));
    }

    public function storeInputData(Request $request, $token)
    {
        $rec = BgRecommendation::with('customer')->where('token', $token)->firstOrFail();

        if (!$rec) {
             return view('page.customer_portal.form-invalid');
        }

        if ($rec->status != 'process') {
            return view('page.customer_portal.expired_or_completed');
        }

        $request->validate([
            'details' => 'required|array',
            'details.*.bank_name' => 'required',
            'details.*.nominal' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $creatorId = $rec->customer ? $rec->customer->user_id : null;
            $createdBgs = [];
            $latestBg = BankGaransi::where('customer_id', $rec->customer_id)
                                   ->orderBy('id', 'desc')
                                   ->first();
            $currentBaseBgId = $latestBg ? $latestBg->id : null;
            $currentYear = date('Y');
            $existingCount = BankGaransi::where('customer_id', $rec->customer_id)
                                ->whereYear('created_at', $currentYear)
                                ->count();

            $financeUser = User::role('head-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head Tidak Diketahui';
            $generatedCount = 0;

            foreach ($request->details as $index => $d) {
                $nominal = (float) $d['nominal'];
                $timestamp = now();
                $sequence = $existingCount + ($index + 1);
                $seqStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);
                $bgNumber = "BG-{$currentYear}-{$seqStr}";

                $bg = BankGaransi::create([
                    'customer_id' => $rec->customer_id,
                    'bg_number'   => $bgNumber,
                    'bg_type'     => 'new',
                    'bg_nominal'  => $nominal,
                    'base_bg_id'  => $currentBaseBgId,
                    'status'      => 'draft',
                    'created_by'  => $creatorId,
                    'created_at'  => $timestamp,
                    'updated_at'  => $timestamp
                ]);

                if (!$currentBaseBgId) {
                    $bg->update(['base_bg_id' => $bg->id]);
                }

                $currentBaseBgId = $bg->id;

                $bg->details()->create([
                    'bank_name'      => $d['bank_name'],
                    'branch_name'    => $d['branch_name'] ?? null,
                    'bank_address'   => $d['bank_address'] ?? null,
                    'contact_person' => $d['contact_person'] ?? null,
                    'nominal'        => $nominal,
                ]);

                $createdBgs[] = $bg;

                $formCode = 'SUB-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . '-' . ($index+1);
                $submission = BgSubmission::create([
                    'bg_recommendation_id' => $rec->id,
                    'form_code' => $formCode,
                    'status' => 'awaiting_upload',
                    'token'  => Str::random(60),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                $pdf = Pdf::loadView('pdf.bg_submission_document', [
                    'bg' => $bg,
                    'bgs' => [$bg],
                    'customer' => $rec->customer,
                    'submission' => $submission,
                    'finance_name' => $financeName
                ]);

                $fileName = 'Formulir_BG_' . str_replace(['/', '\\'], '-', $submission->form_code) . '.pdf';
                Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

                if ($rec->customer && $rec->customer->email) {
                    $pdfContentBase64 = base64_encode($pdf->output());

                    Mail::to($rec->customer->email)
                        ->queue(new BgSubmissionDocumentMail($submission, $pdfContentBase64));
                }

                $generatedCount++;
            }

            $rec->update([
                'status' => 'waiting_upload',
                'notes'  => $rec->notes,
                'token'  => null
            ]);

            DB::commit();

            $downloadUrl = route('customer.portal.download-pdf', ['token' => $submission->token]);

            return view('page.customer_portal.form-success', [
                'type'        => 'input_multi',
                'downloadUrl' => $downloadUrl,
                'uploadToken' => $submission->token,
                'message'     => 'Berhasil! Kami telah mengirimkan ' . $generatedCount . ' email terpisah untuk setiap bank. Silakan cek email Anda untuk mengunduh dan mengupload dokumen masing-masing.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadPdf($token)
    {
        try {
            $submission = BgSubmission::where('token', $token)->firstOrFail();
            $fileName = 'Formulir_BG_' . str_replace(['/', '\\'], '-', $submission->form_code) . '.pdf';
            $path = 'generated_pdfs/' . $fileName;

            // 1. Coba ambil file fisik dulu (paling aman karena dibuat saat loop)
            if (Storage::disk('public')->exists($path)) {
                return response()->download(storage_path('app/public/' . $path), $fileName);
            }

            // 2. Fallback: Generate ulang jika file hilang (Logic Index Matching)
            $rec = $submission->recommendation;
            
            // Cari submission temannya yang dibuat barengan
            $siblings = BgSubmission::where('bg_recommendation_id', $rec->id)
                        ->where('created_at', $submission->created_at)
                        ->orderBy('id', 'asc')
                        ->pluck('id')->toArray();
            
            // Saya urutan keberapa?
            $myIndex = array_search($submission->id, $siblings);

            // Ambil kandidat BG
            $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                            ->where('created_at', $submission->created_at)
                            ->orderBy('id', 'asc')
                            ->get();

            // Ambil BG yang sesuai urutan
            $bg = isset($candidateBgs[$myIndex]) ? $candidateBgs[$myIndex] : $candidateBgs->first();

            $pdf = Pdf::loadView('pdf.bg_submission_document', [
                'bg' => $bg,
                'customer' => $rec->customer,
                'submission' => $submission
            ]);

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            abort(404, 'File dokumen tidak ditemukan.');
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
        $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $rec->id)
                                ->where('created_at', $submission->created_at)
                                ->orderBy('id', 'asc')
                                ->pluck('id')
                                ->toArray();

        $myIndex = array_search($submission->id, $siblingSubmissions);
        $candidateBgs = BankGaransi::where('customer_id', $rec->customer_id)
                            ->where('created_at', $submission->created_at)
                            ->with('details')
                            ->orderBy('id', 'asc')
                            ->get();

        if (isset($candidateBgs[$myIndex])) {
            $bg = $candidateBgs[$myIndex];
        } else {
            $bg = $candidateBgs->first();
        }

        return view('page.customer_portal.upload_form', compact('submission', 'token', 'bg'));
    }

    public function storeUploadData(Request $request, $token)
    {
        $submission = BgSubmission::where('token', $token)->firstOrFail();

        if ($submission->status != 'awaiting_upload') {
            return back()->with('error', 'Dokumen sudah diupload sebelumnya.');
        }

        $request->validate([
            'signed_document' => 'required|mimes:pdf|max:2048',
        ]);

        try {
            $file = $request->file('signed_document');
            $path = $file->store('bg_documents/signed', 'public');

            $submission->update([
                'signed_document_path' => 'storage/' . $path,
                'submitted_at'         => now(),
                'upload_completed_at'  => now(),
                'status'               => 'uploaded',
                'token'                => null,
            ]);

            return view('page.customer_portal.form-success', [
                'type' => 'upload'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
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

            $submissionDates = BgSubmission::where('bg_recommendation_id', $rec->id)->pluck('created_at');
            $totalBgDiserahkan = BankGaransi::where('customer_id', $customer->id)
                                    ->whereIn('created_at', $submissionDates)
                                    ->sum('bg_nominal');

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
            abort(404, 'Dokumen tidak ditemukan.');
        }
    }
}
