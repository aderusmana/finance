<?php

namespace App\Services\Distributor;

use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocument;
use App\Models\Customer\DistributorDocumentAttachment;
use App\Models\Customer\DistributorDocumentDownload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;
use ZipArchive;

class DistributorDocumentService
{
    /**
     * Get Eloquent query for distributor document monitoring list.
     */
    public function getMonitoringDistributorsQuery(int $year): Builder
    {
        return Distributor::query()
            ->select('distributors.*')
            ->whereHas('documents', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->with([
                'customer',
                'documents' => function ($q) use ($year) {
                    $q->where('year', $year)->with('attachments');
                },
                'transferDocuments' => function ($q) {
                    $q->select('id', 'distributor_id', 'transaction_date', 'created_at');
                },
            ])
            ->withCount(['transferDocuments']);
    }

    /**
     * Get Eloquent query for running transfer documents of a distributor.
     */
    public function getTransferDocumentsQuery(int $distributorId, $transferYear = 'all'): Builder
    {
        $query = DistributorDocumentAttachment::runningTransfers($distributorId);

        if ($transferYear && $transferYear !== 'all') {
            $transferYearInt = (int) $transferYear;
            $query->where(function ($q) use ($transferYearInt) {
                $q->whereYear('transaction_date', $transferYearInt)
                    ->orWhere(function ($sub) use ($transferYearInt) {
                        $sub->whereNull('transaction_date')
                            ->whereYear('created_at', $transferYearInt);
                    });
            });
        }

        return $query;
    }

    /**
     * Build response for running transfer documents.
     */
    public function getTransferDataTable(
        Distributor $distributor,
        $transferYear = 'all',
        bool $isPortal = false,
        bool $canManage = false
    ): JsonResponse {
        $query = $this->getTransferDocumentsQuery($distributor->id, $transferYear);

        return DataTables::eloquent($query)
            ->addColumn('formatted_date', function ($doc) {
                if ($doc->transaction_date) {
                    return '<div class="fw-semibold text-nowrap">'.e($doc->transaction_date->format('d M Y')).'</div>';
                } elseif ($doc->year) {
                    return '<div class="fw-semibold text-nowrap">Tahun '.e((string) $doc->year).'</div>';
                }

                return '<span class="text-muted">-</span>';
            })
            ->addColumn('file_display', function ($doc) {
                return view('page.distributor_documents.partials.document-file', [
                    'document' => $doc,
                ])->render();
            })
            ->addColumn('notes', function ($doc) {
                return '<span class="text-secondary">'.e($doc->notes ?: '-').'</span>';
            })
            ->addColumn('file_size_badge', function ($doc) {
                return '<span class="badge bg-light text-dark border">'.e($doc->human_file_size).'</span>';
            })
            ->addColumn('actions', function ($doc) use ($isPortal, $canManage) {
                return view('page.distributor_documents.partials.document-actions', [
                    'document' => $doc,
                    'isPortal' => $isPortal,
                    'canManage' => $canManage,
                    'year' => $doc->year ?: (int) date('Y'),
                    'tab' => 'transfer',
                ])->render();
            })
            ->filter(function ($query) {
                if ($keyword = request()->input('search.value')) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%")
                            ->orWhere('file_name', 'like', "%{$keyword}%")
                            ->orWhere('notes', 'like', "%{$keyword}%")
                            ->orWhereYear('transaction_date', $keyword)
                            ->orWhereYear('created_at', $keyword);
                    });
                }
            })
            ->orderColumn('formatted_date', function ($query, $order) {
                $query->orderBy('transaction_date', $order)->orderBy('created_at', $order);
            })
            ->orderColumn('file_size_badge', 'file_size $1')
            ->rawColumns(['formatted_date', 'file_display', 'notes', 'file_size_badge', 'actions'])
            ->make(true);
    }

    /**
     * Fetch organized document detail data for both internal and public portal views.
     */
    public function getDetailData(Distributor $distributor, int $year, string $tab = 'monthly', $transferYear = 'all'): array
    {
        $validTab = in_array($tab, ['monthly', 'transfer'], true) ? $tab : 'monthly';

        // Monthly docs for selected year
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

        // Count of all running transfer documents for tab badge
        $transferDocsCount = DistributorDocumentAttachment::where('distributor_id', $distributor->id)
            ->where('doc_type', 'transfer')
            ->whereNotNull('file_path')
            ->count();

        // Distinct available years for transfer filter
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

        return [
            'monthlyDocs' => $monthlyDocs,
            'transferDocs' => collect(),
            'transferDocsCount' => $transferDocsCount,
            'tab' => $validTab,
            'transferYear' => $transferYear,
            'availableTransferYears' => $availableTransferYears,
        ];
    }

    /**
     * Store and register an uploaded document file.
     */
    public function uploadDocument(
        Distributor $distributor,
        array $data,
        UploadedFile $file,
        ?int $uploadedBy = null
    ): DistributorDocumentAttachment {
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $isTransfer = $data['doc_type'] === 'transfer';

        $year = ! empty($data['year'])
            ? (int) $data['year']
            : (! empty($data['transaction_date']) ? (int) date('Y', strtotime($data['transaction_date'])) : (int) date('Y'));

        $folderPath = "distributor_docs/{$distributor->code}/{$data['doc_type']}";
        $hashedFilename = Str::uuid().'.'.$extension;
        $storedPath = $file->storeAs($folderPath, $hashedFilename, 'public');

        if (! $storedPath) {
            abort(500, 'Dokumen gagal disimpan di server.');
        }

        try {
            return DB::transaction(function () use (
                $distributor,
                $data,
                $year,
                $isTransfer,
                $originalName,
                $storedPath,
                $extension,
                $fileSize,
                $mimeType,
                $uploadedBy
            ) {
                $docHeader = DistributorDocument::firstOrCreate([
                    'distributor_id' => $distributor->id,
                    'year' => $year,
                ]);

                return DistributorDocumentAttachment::create([
                    'distributor_document_id' => $docHeader->id,
                    'distributor_id' => $distributor->id,
                    'doc_type' => $data['doc_type'],
                    'month' => $isTransfer ? null : ($data['month'] ?? null),
                    'title' => $data['title'] ?? $originalName,
                    'file_name' => $originalName,
                    'file_path' => $storedPath,
                    'file_ext' => $extension,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'transaction_date' => $isTransfer ? ($data['transaction_date'] ?? null) : null,
                    'notes' => $data['notes'] ?? null,
                    'uploaded_by' => $uploadedBy ?? auth()->id(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPath);
            throw $exception;
        }
    }

    /**
     * Register a distributor to document monitoring list for a specific year, with optional file upload.
     */
    public function registerDistributorToYear(
        Distributor $distributor,
        int $year,
        bool $withUpload = false,
        ?array $uploadData = null,
        ?UploadedFile $file = null,
        ?int $uploadedBy = null
    ): DistributorDocument {
        $alreadyInList = DistributorDocument::where('distributor_id', $distributor->id)
            ->where('year', $year)
            ->exists();

        if ($alreadyInList) {
            throw new \InvalidArgumentException("Distributor '{$distributor->name}' sudah terdaftar dalam list dokumen tahun {$year}.");
        }

        return DB::transaction(function () use ($distributor, $year, $withUpload, $uploadData, $file, $uploadedBy) {
            $docHeader = DistributorDocument::create([
                'distributor_id' => $distributor->id,
                'year' => $year,
            ]);

            if ($withUpload && $file && $uploadData) {
                $this->uploadDocument($distributor, array_merge($uploadData, ['year' => $year]), $file, $uploadedBy);
            }

            return $docHeader;
        });
    }

    /**
     * Delete an attachment file from storage and database.
     */
    public function deleteAttachment(DistributorDocumentAttachment $attachment): void
    {
        if ($attachment->existsOnDisk()) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();
    }

    /**
     * Generate inline preview response for document attachment.
     */
    public function previewResponse(DistributorDocumentAttachment $attachment): BinaryFileResponse
    {
        if (! $attachment->existsOnDisk()) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $filePath = $attachment->getDiskPath();
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

    /**
     * Generate single file download response with audit logging.
     */
    public function downloadResponse(
        DistributorDocumentAttachment $attachment,
        string $via,
        ?Request $request = null
    ): BinaryFileResponse {
        if (! $attachment->existsOnDisk()) {
            abort(404, 'File tidak ditemukan.');
        }

        DistributorDocumentDownload::record(
            $attachment->id,
            $attachment->distributor_id,
            $via,
            $request
        );

        return response()->download($attachment->getDiskPath(), $attachment->file_name);
    }

    /**
     * Generate ZIP archive download response with audit logging for all included files.
     */
    public function downloadZipResponse(
        Distributor $distributor,
        int $year,
        string $via,
        ?Request $request = null
    ): BinaryFileResponse {
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
            abort(404, 'Tidak ada dokumen yang dapat diunduh.');
        }

        $tempDirPath = storage_path('app/temp');
        if (! file_exists($tempDirPath)) {
            mkdir($tempDirPath, 0755, true);
        }

        $zipFileName = "Dokumen_{$distributor->code}_{$year}_".time().'.zip';
        $zipFilePath = "{$tempDirPath}/{$zipFileName}";

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat berkas ZIP.');
        }

        foreach ($attachments as $att) {
            if ($att->existsOnDisk()) {
                $realPath = $att->getDiskPath();

                if ($att->doc_type === 'transfer') {
                    $zipFolder = 'Penjelasan Transfer Running/';
                } else {
                    $monthName = sprintf('%02d', $att->month).' - '.($att->month_name ?? "Bulan {$att->month}");
                    $typeFolder = $att->doc_type === 'bupot' ? 'Bukti Potong' : 'TOP Insentif';
                    $zipFolder = "Dokumen Bulanan {$year}/{$monthName}/{$typeFolder}/";
                }

                $zip->addFile($realPath, $zipFolder.$att->file_name);

                DistributorDocumentDownload::record(
                    $att->id,
                    $att->distributor_id,
                    $via,
                    $request
                );
            }
        }

        $zip->close();

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
