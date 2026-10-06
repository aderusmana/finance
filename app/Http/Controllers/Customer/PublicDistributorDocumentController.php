<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PublicDistributorDocumentController extends Controller
{
    /**
     * Processing verification of distributor document search from public pages.
     */
    public function searchDocuments(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'exists:distributors,code'],
            'year' => ['required', 'integer', 'digits:4'],
        ], [
            'code.exists' => 'Kode Distributor tidak ditemukan.',
            'code.required' => 'Kode Distributor wajib diisi.',
            'year.required' => 'Tahun dokumen wajib dipilih.',
        ]);

        $distributor = Distributor::where('code', $request->code)->firstOrFail();
        $year = (int) $request->year;

        // get monthly docs (BuPot & TOP) for the selected year
        $monthlyDocs = DistributorDocument::where('distributor_id', $distributor->id)
            ->whereNotNull('file_path')
            ->whereIn('doc_type', ['bupot', 'top_insentif'])
            ->where('year', $year)
            ->get()
            ->groupBy(['month', 'doc_type']);

        // get all running transfer explanation documents (multi-year)
        $transferDocs = DistributorDocument::runningTransfers($distributor->id)->get();

        return view('auth.portal_distributor_result', compact('distributor', 'year', 'monthlyDocs', 'transferDocs'));
    }

    /**
     * Preview file PDF.
     */
    public function previewFile($id)
    {
        $document = DistributorDocument::findOrFail($id);

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => $document->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$document->file_name.'"',
        ]);
    }

    /**
     * Download single file while recording download audit trail.
     */
    public function downloadFile(Request $request, $id)
    {
        $document = DistributorDocument::findOrFail($id);

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Record download audit trail to database
        DB::table('distributor_document_downloads')->insert([
            'distributor_document_id' => $document->id,
            'distributor_id' => $document->distributor_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'downloaded_via' => auth()->check() ? 'internal' : 'guest_portal',
            'downloaded_at' => now(),
        ]);

        $filePath = Storage::disk('public')->path($document->file_path);

        return response()->download($filePath, $document->file_name);
    }

    /**
     * Bundling download documents to zip file using PHP ZipArchive.
     */
    public function downloadAllZip(Request $request)
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);
        $year = (int) $request->year;

        // get monthly documents for this year + all running transfer files
        $documents = DistributorDocument::where('distributor_id', $distributor->id)
            ->whereNotNull('file_path')
            ->where(function ($query) use ($year) {
                $query->where(function ($q) use ($year) {
                    $q->whereIn('doc_type', ['bupot', 'top_insentif'])
                        ->where('year', $year);
                })->orWhere('doc_type', 'transfer');
            })
            ->get();

        if ($documents->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada dokumen yang dapat diunduh.');
        }

        // Name & location of temporary ZIP directory
        $zipFileName = "Dokumen_{$distributor->code}_{$year}_".time().'.zip';
        $tempDirPath = storage_path('app/temp');

        if (! file_exists($tempDirPath)) {
            mkdir($tempDirPath, 0755, true);
        }

        $zipFilePath = "{$tempDirPath}/{$zipFileName}";

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            foreach ($documents as $doc) {
                if (Storage::disk('public')->exists($doc->file_path)) {
                    $realPath = Storage::disk('public')->path($doc->file_path);

                    // Folder structure inside ZIP
                    if ($doc->doc_type === 'transfer') {
                        $zipFolder = 'Penjelasan Transfer Running/';
                    } else {
                        $monthName = sprintf('%02d', $doc->month).' - '.($doc->month_name ?? "Bulan {$doc->month}");
                        $typeFolder = $doc->doc_type === 'bupot' ? 'Bukti Potong' : 'TOP Insentif';
                        $zipFolder = "Dokumen Bulanan {$year}/{$monthName}/{$typeFolder}/";
                    }

                    // Add file to ZIP with its original name
                    $zip->addFile($realPath, $zipFolder.$doc->file_name);

                    // Log Audit Trail for each file included in the ZIP download
                    DB::table('distributor_document_downloads')->insert([
                        'distributor_document_id' => $doc->id,
                        'distributor_id' => $doc->distributor_id,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'downloaded_via' => auth()->check() ? 'internal' : 'guest_portal',
                        'downloaded_at' => now(),
                    ]);
                }
            }

            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Gagal membuat berkas ZIP.');
        }

        // Return stream download then delete temp file after sending
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
