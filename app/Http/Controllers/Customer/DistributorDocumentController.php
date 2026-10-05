<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistributorDocumentRequest;
use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DistributorDocumentController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $search = $request->input('search');

        $distributors = Distributor::query()
            ->with('customer')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->withCount(['transferDocuments'])
            ->with(['transferDocuments' => function ($q) {
                $q->select('id', 'distributor_id', 'year', 'transaction_date');
            }])
            ->paginate(15);

        // Map status 12 bulan dan range tahun transfer per distributor
        $distributors->getCollection()->transform(function ($distributor) use ($year) {
            $distributor->monthly_summary = $distributor->getMonthlyDocumentSummary($year);

            $transferYears = $distributor->transferDocuments->map(function ($doc) {
                if ($doc->transaction_date) {
                    return (int) $doc->transaction_date->format('Y');
                }

                return $doc->year ? (int) $doc->year : null;
            })->filter()->unique()->sort()->values();

            if ($transferYears->isNotEmpty()) {
                $minY = $transferYears->first();
                $maxY = $transferYears->last();
                $distributor->transfer_range = $minY === $maxY ? (string) $minY : "{$minY}-{$maxY}";
            } else {
                $distributor->transfer_range = null;
            }

            return $distributor;
        });

        return view('finance.distributor_documents.index', compact('distributors', 'year', 'search'));
    }

    public function detailView(Request $request, $distributorId)
    {
        $distributor = Distributor::with('customer')->findOrFail($distributorId);
        $year = (int) $request->input('year', date('Y'));
        $transferYear = $request->input('transfer_year');
        $tab = $request->input('tab', 'monthly');
        $tab = in_array($tab, ['monthly', 'transfer'], true) ? $tab : 'monthly';

        $monthlyDocs = DistributorDocument::where('distributor_id', $distributor->id)
            ->whereIn('doc_type', ['bupot', 'top_insentif'])
            ->where('year', $year)
            ->get()
            ->groupBy(['month', 'doc_type']);

        $transferDocsQuery = DistributorDocument::runningTransfers($distributor->id);
        if ($transferYear && $transferYear !== 'all') {
            $transferDocsQuery->where(function ($q) use ($transferYear) {
                $q->whereYear('transaction_date', $transferYear)
                    ->orWhere('year', $transferYear);
            });
        }
        $transferDocs = $transferDocsQuery->get();

        // List year for transfer documents (dropdown)
        $availableTransferYears = DistributorDocument::where('distributor_id', $distributor->id)
            ->where('doc_type', 'transfer')
            ->get()
            ->map(function ($doc) {
                return $doc->transaction_date ? (int) $doc->transaction_date->format('Y') : ($doc->year ? (int) $doc->year : null);
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        // Return partial view if request via AJAX (Modal Detail)
        if ($request->ajax() || $request->has('ajax')) {
            return view('finance.distributor_documents.partials.detail-content', compact(
                'distributor', 'year', 'tab', 'monthlyDocs', 'transferDocs', 'transferYear', 'availableTransferYears'
            ));
        }

        return view('finance.distributor_documents.detail', compact(
            'distributor', 'year', 'tab', 'monthlyDocs', 'transferDocs', 'transferYear', 'availableTransferYears'
        ));
    }

    public function storeUpload(StoreDistributorDocumentRequest $request)
    {
        $validated = $request->validated();
        $distributor = Distributor::findOrFail($validated['distributor_id']);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $year = $validated['year'] ?? (int) date('Y');
        $isTransfer = $validated['doc_type'] === 'transfer';

        $folderPath = "distributor_docs/{$distributor->code}/{$validated['doc_type']}";
        $hashedFilename = Str::uuid().'.'.$extension;
        $storedPath = $file->storeAs($folderPath, $hashedFilename, 'public');

        if (! $storedPath) {
            abort(500, 'Dokumen gagal disimpan.');
        }

        try {
            DistributorDocument::create([
                'distributor_id' => $distributor->id,
                'doc_type' => $validated['doc_type'],
                'year' => $year,
                'month' => $isTransfer ? null : $validated['month'],
                'title' => $validated['title'] ?? $originalName,
                'file_name' => $originalName,
                'file_path' => $storedPath,
                'file_ext' => $extension,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'transaction_date' => $isTransfer ? $validated['transaction_date'] : null,
                'notes' => $validated['notes'] ?? null,
                'uploaded_by' => auth()->id(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPath);
            throw $exception;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah.',
                'distributor_id' => $distributor->id,
                'year' => $year,
                'tab' => $isTransfer ? 'transfer' : 'monthly',
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $distributor->id,
            'year' => $year,
            'tab' => $isTransfer ? 'transfer' : 'monthly',
        ])->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroy(Request $request, $id)
    {
        $document = DistributorDocument::findOrFail($id);
        $distributorId = $document->distributor_id;
        $year = $document->year ?? date('Y');

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus.',
                'distributor_id' => $distributorId,
                'year' => $year,
                'tab' => $document->doc_type === 'transfer' ? 'transfer' : 'monthly',
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $distributorId,
            'year' => $year,
            'tab' => $document->doc_type === 'transfer' ? 'transfer' : 'monthly',
        ])->with('success', 'Dokumen berhasil dihapus.');
    }

    public function previewFile($id)
    {
        $document = DistributorDocument::findOrFail($id);

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        $allowedMimeTypes = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
        ];
        $mimeType = in_array($document->mime_type, $allowedMimeTypes, true)
            ? $document->mime_type
            : 'application/octet-stream';
        $fileName = str_replace(['"', "\r", "\n"], '', $document->file_name);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

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

    public function downloadAllZip(Request $request)
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);
        $year = (int) $request->year;

        $documents = DistributorDocument::where('distributor_id', $distributor->id)
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

                    if ($doc->doc_type === 'transfer') {
                        $zipFolder = 'Penjelasan Transfer Running/';
                    } else {
                        $monthName = sprintf('%02d', $doc->month).' - '.($doc->month_name ?? "Bulan {$doc->month}");
                        $typeFolder = $doc->doc_type === 'bupot' ? 'Bukti Potong' : 'TOP Insentif';
                        $zipFolder = "Dokumen Bulanan {$year}/{$monthName}/{$typeFolder}/";
                    }

                    $zip->addFile($realPath, $zipFolder.$doc->file_name);

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

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
