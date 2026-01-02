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
    /**
     * STEP 1: Tampilkan Form Input Detail
     */
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

    /**
     * STEP 2: Simpan Data, Generate PDF, Create Submission, Kirim Email
     */
    public function storeInputData(Request $request, $token)
    {
        $rec = BgRecommendation::with('customer')->where('token', $token)->firstOrFail();
        $submission = BgSubmission::where('bg_recommendation_id', $rec->id)->first();

        if (!$submission) {
             return back()->with('error', 'Data submission tidak ditemukan. Hubungi Admin.');
        }

        if ($submission->status != 'pending_print') {
            return back()->with('error', 'Formulir ini sudah disubmit sebelumnya atau sedang dalam proses.');
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

            foreach ($request->details as $index => $d) {
                $nominal = (float) $d['nominal'];
                $sequence = $existingCount + ($index + 1);
                $seqStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);
                $bgNumber = "BG-{$currentYear}-{$seqStr}";

                $bg = BankGaransi::create([
                    'customer_id' => $rec->customer_id,
                    'bg_number'   => $bgNumber,
                    'bg_type'     => 'new',
                    'bg_nominal'  => $nominal,
                    'base_bg_id'  => $currentBaseBgId,
                    'status'      => 'submitted',
                    'created_by'  => $creatorId,
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
            }

            $submission->update([
                'status' => 'awaiting_upload'
            ]);

            $financeUser = User::role('manager-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Manager Finance';

            $pdf = Pdf::loadView('pdf.bg_submission_document', [
                'bg' => $createdBgs[0],
                'bgs' => $createdBgs,
                'customer' => $rec->customer,
                'submission' => $submission,
                'finance_name' => $financeName
            ]);

            $fileName = 'Formulir_BG_' . str_replace(['/', '\\'], '-', $submission->form_code) . '.pdf';
            Storage::disk('public')->put('generated_pdfs/' . $fileName, $pdf->output());

            $pdfContentBase64 = base64_encode($pdf->output());
            if ($rec->customer && $rec->customer->email) {
                Mail::to($rec->customer->email)
                    ->queue(new BgSubmissionDocumentMail($submission, $pdfContentBase64));
            }

            $rec->update([
                'status' => 'waiting_upload',
                'notes'  => $rec->notes,
                'token'  => null
            ]);

            DB::commit();

            $downloadUrl = route('customer.portal.download-pdf', ['token' => $submission->token]);

            return view('page.customer_portal.form-success', [
                'type'        => 'input',
                'downloadUrl' => $downloadUrl,
                'uploadToken' => $submission->token
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

            if (Storage::disk('public')->exists($path)) {
                return response()->download(storage_path('app/public/' . $path), $fileName);
            }
            $rec = $submission->recommendation;
            $bg = BankGaransi::where('bg_number', 'like', '%'.$submission->form_code.'%')->first();

            if(!$bg) {
                 $bg = BankGaransi::where('created_at', $submission->created_at)->first();
            }

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

        $bg = BankGaransi::where('customer_id', $submission->recommendation->customer_id)
                         ->where('status', 'submitted')
                         ->latest()
                         ->with('details')
                         ->first();

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
            $financeUser = User::role('manager-finance')->first();
            $financeName = $financeUser ? $financeUser->name : 'Manager Finance';
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
}
