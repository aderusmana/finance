<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistributorDocumentRequest;
use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocument;
use App\Models\Customer\DistributorDocumentAttachment;
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
            ->whereHas('documents', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->with('customer')
            ->with(['documents' => function ($q) use ($year) {
                $q->where('year', $year)->with('attachments');
            }])
            ->withCount(['transferDocuments'])
            ->with(['transferDocuments' => function ($q) {
                $q->select('id', 'distributor_id', 'transaction_date', 'created_at');
            }])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('distributors.name', 'like', "%{$search}%")
                        ->orWhere('distributors.code', 'like', "%{$search}%");
                });
            })
            ->orderBy('distributors.name', 'asc')
            ->paginate(15);

        // Map 12-month status and transfer year range per distributor
        $distributors->getCollection()->transform(function ($distributor) use ($year) {
            $distributor->monthly_summary = $distributor->getMonthlyDocumentSummary($year);

            $transferYears = $distributor->transferDocuments->map(function ($doc) {
                if ($doc->transaction_date) {
                    return (int) $doc->transaction_date->format('Y');
                }

                return (int) $doc->created_at->format('Y');
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

        // Get all distributors for the Add Distributor modal dropdown option
        $availableDistributors = Distributor::orderBy('code', 'asc')->get(['id', 'code', 'name', 'email', 'bupot_email']);

        return view('finance.distributor_documents.index', compact('distributors', 'year', 'search', 'availableDistributors'));
    }

    public function detailView(Request $request, $distributorId)
    {
        $distributor = Distributor::with('customer')->findOrFail($distributorId);
        $year = (int) $request->input('year', date('Y'));
        $transferYear = $request->input('transfer_year');
        $tab = $request->input('tab', 'monthly');
        $tab = in_array($tab, ['monthly', 'transfer'], true) ? $tab : 'monthly';

        // Header document for selected year
        $yearDoc = DistributorDocument::where('distributor_id', $distributor->id)
            ->where('year', $year)
            ->first();

        $monthlyDocs = $yearDoc
            ? $yearDoc->attachments()
                ->whereIn('doc_type', ['bupot', 'top_insentif'])
                ->whereNotNull('file_path')
                ->get()
                ->groupBy(['month', 'doc_type'])
            : collect();

        $transferDocsQuery = DistributorDocumentAttachment::runningTransfers($distributor->id);
        if ($transferYear && $transferYear !== 'all') {
            $transferDocsQuery->where(function ($q) use ($transferYear) {
                $q->whereYear('transaction_date', $transferYear)
                    ->orWhere(function ($sub) use ($transferYear) {
                        $sub->whereNull('transaction_date')
                            ->whereYear('created_at', $transferYear);
                    });
            });
        }
        $transferDocs = $transferDocsQuery->get();

        // List of years for transfer documents (dropdown filter)
        $availableTransferYears = DistributorDocumentAttachment::where('distributor_id', $distributor->id)
            ->where('doc_type', 'transfer')
            ->whereNotNull('file_path')
            ->get()
            ->map(function ($doc) {
                return $doc->transaction_date
                    ? (int) $doc->transaction_date->format('Y')
                    : (int) $doc->created_at->format('Y');
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
        $isTransfer = $validated['doc_type'] === 'transfer';

        // Determine year for DistributorDocument header
        $year = ! empty($validated['year'])
            ? (int) $validated['year']
            : (! empty($validated['transaction_date']) ? (int) date('Y', strtotime($validated['transaction_date'])) : (int) date('Y'));

        $folderPath = "distributor_docs/{$distributor->code}/{$validated['doc_type']}";
        $hashedFilename = Str::uuid().'.'.$extension;
        $storedPath = $file->storeAs($folderPath, $hashedFilename, 'public');

        if (! $storedPath) {
            abort(500, 'Dokumen gagal disimpan.');
        }

        try {
            DB::transaction(function () use ($distributor, $validated, $year, $isTransfer, $originalName, $storedPath, $extension, $fileSize, $mimeType) {
                // Ensure DistributorDocument header for this year exists
                $docHeader = DistributorDocument::firstOrCreate([
                    'distributor_id' => $distributor->id,
                    'year' => $year,
                ]);

                // Save physical file to attachment table
                DistributorDocumentAttachment::create([
                    'distributor_document_id' => $docHeader->id,
                    'distributor_id' => $distributor->id,
                    'doc_type' => $validated['doc_type'],
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
            });
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
        $attachment = DistributorDocumentAttachment::with('document')->findOrFail($id);
        $distributorId = $attachment->distributor_id;
        $year = $attachment->document->year ?? date('Y');
        $docType = $attachment->doc_type;

        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        // Header DistributorDocument still saved in database, so distributor
        // still appears in the monitoring list for that year even if all its files have been deleted.

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus.',
                'distributor_id' => $distributorId,
                'year' => $year,
                'tab' => $docType === 'transfer' ? 'transfer' : 'monthly',
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $distributorId,
            'year' => $year,
            'tab' => $docType === 'transfer' ? 'transfer' : 'monthly',
        ])->with('success', 'Dokumen berhasil dihapus.');
    }

    public function previewFile($id)
    {
        $attachment = DistributorDocumentAttachment::findOrFail($id);

        if (! $attachment->file_path || ! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $filePath = Storage::disk('public')->path($attachment->file_path);
        $allowedMimeTypes = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
        ];
        $mimeType = in_array($attachment->mime_type, $allowedMimeTypes, true)
            ? $attachment->mime_type
            : 'application/octet-stream';
        $fileName = str_replace(['"', "\r", "\n"], '', $attachment->file_name);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function downloadFile(Request $request, $id)
    {
        $attachment = DistributorDocumentAttachment::findOrFail($id);

        if (! $attachment->file_path || ! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Record download audit trail to database
        DB::table('distributor_document_downloads')->insert([
            'distributor_document_attachment_id' => $attachment->id,
            'distributor_id' => $attachment->distributor_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'downloaded_via' => auth()->check() ? 'internal' : 'guest_portal',
            'downloaded_at' => now(),
        ]);

        $filePath = Storage::disk('public')->path($attachment->file_path);

        return response()->download($filePath, $attachment->file_name);
    }

    public function downloadAllZip(Request $request)
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);
        $year = (int) $request->year;

        // Get monthly document attachments for selected year + all running transfer files
        $attachments = DistributorDocumentAttachment::where('distributor_id', $distributor->id)
            ->whereNotNull('file_path')
            ->where(function ($query) use ($year) {
                $query->where(function ($q) use ($year) {
                    $q->whereIn('doc_type', ['bupot', 'top_insentif'])
                        ->whereHas('document', function ($docQ) use ($year) {
                            $docQ->where('year', $year);
                        });
                })->orWhere('doc_type', 'transfer');
            })
            ->with('document')
            ->get();

        if ($attachments->isEmpty()) {
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
            foreach ($attachments as $att) {
                if ($att->file_path && Storage::disk('public')->exists($att->file_path)) {
                    $realPath = Storage::disk('public')->path($att->file_path);

                    if ($att->doc_type === 'transfer') {
                        $zipFolder = 'Penjelasan Transfer Running/';
                    } else {
                        $monthName = sprintf('%02d', $att->month).' - '.($att->month_name ?? "Bulan {$att->month}");
                        $typeFolder = $att->doc_type === 'bupot' ? 'Bukti Potong' : 'TOP Insentif';
                        $zipFolder = "Dokumen Bulanan {$year}/{$monthName}/{$typeFolder}/";
                    }

                    $zip->addFile($realPath, $zipFolder.$att->file_name);

                    DB::table('distributor_document_downloads')->insert([
                        'distributor_document_attachment_id' => $att->id,
                        'distributor_id' => $att->distributor_id,
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

    public function storeDistributorList(Request $request)
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer', 'digits:4'],
            'with_upload' => ['nullable', 'boolean'],
        ], [
            'distributor_id.required' => 'Distributor wajib dipilih.',
            'distributor_id.exists' => 'Distributor tidak valid.',
            'year.required' => 'Tahun dokumen wajib diisi.',
            'year.digits' => 'Format tahun harus 4 digit.',
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);
        $year = (int) $request->year;
        $withUpload = $request->boolean('with_upload');

        // Check unique (distributor_id, year) in distributor_documents table
        $alreadyInList = DistributorDocument::where('distributor_id', $distributor->id)
            ->where('year', $year)
            ->exists();

        if ($alreadyInList) {
            return response()->json([
                'success' => false,
                'message' => "Distributor '{$distributor->name}' sudah terdaftar dalam list dokumen tahun {$year}.",
            ], 422);
        }

        if ($withUpload) {
            $request->validate([
                'doc_type' => ['required', 'in:bupot,transfer,top_insentif'],
                'month' => [
                    'required_if:doc_type,bupot,top_insentif',
                    'nullable',
                    'integer',
                    'between:1,12',
                ],
                'transaction_date' => [
                    'required_if:doc_type,transfer',
                    'nullable',
                    'date',
                ],
                'title' => ['nullable', 'string', 'max:255'],
                'notes' => ['nullable', 'string'],
                'file' => ['required', 'file', 'mimes:pdf', 'max:1024'],
            ], [
                'file.required' => 'Berkas PDF wajib diunggah jika opsi upload dicentang.',
                'file.max' => 'Ukuran file tidak boleh melebihi 1 MB.',
                'file.mimes' => 'Format file harus berupa PDF.',
                'doc_type.required' => 'Jenis dokumen wajib dipilih.',
                'transaction_date.required_if' => 'Tanggal transaksi wajib diisi untuk Penjelasan Transfer.',
                'month.required_if' => 'Bulan wajib dipilih untuk BuPot dan TOP Insentif.',
            ]);
        }

        DB::transaction(function () use ($request, $distributor, $year, $withUpload) {
            // Create DistributorDocument header for registration in the corresponding year
            $docHeader = DistributorDocument::create([
                'distributor_id' => $distributor->id,
                'year' => $year,
            ]);

            if ($withUpload && $request->hasFile('file')) {
                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                $isTransfer = $request->doc_type === 'transfer';

                $folderPath = "distributor_docs/{$distributor->code}/{$request->doc_type}";
                $hashedFilename = Str::uuid().'.'.$extension;
                $storedPath = $file->storeAs($folderPath, $hashedFilename, 'public');

                if (! $storedPath) {
                    throw new \RuntimeException('Gagal menyimpan berkas di server.');
                }

                DistributorDocumentAttachment::create([
                    'distributor_document_id' => $docHeader->id,
                    'distributor_id' => $distributor->id,
                    'doc_type' => $request->doc_type,
                    'month' => $isTransfer ? null : $request->month,
                    'title' => $request->title ?? $originalName,
                    'file_name' => $originalName,
                    'file_path' => $storedPath,
                    'file_ext' => $extension,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'transaction_date' => $isTransfer ? $request->transaction_date : null,
                    'notes' => $request->notes ?? null,
                    'uploaded_by' => auth()->id(),
                ]);
            }
        });

        $message = $withUpload
            ? "Distributor '{$distributor->name}' berhasil ditambahkan ke list tahun {$year} dan dokumen berhasil diunggah."
            : "Distributor '{$distributor->name}' berhasil ditambahkan ke list dokumen tahun {$year}.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'year' => $year,
        ]);
    }
}
