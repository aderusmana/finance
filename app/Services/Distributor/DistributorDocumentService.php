<?php

namespace App\Services\Distributor;

use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocument;
use App\Models\Customer\DistributorDocumentAttachment;
use App\Models\Customer\DistributorDocumentDownload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
     * Build DataTables response for distributor document monitoring list.
     */
    public function getMonitoringDataTable(int $year): JsonResponse
    {
        $query = $this->getMonitoringDistributorsQuery($year);
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        return DataTables::eloquent($query)
            ->addColumn('code', function ($distributor) {
                return view('page.distributor_documents.partials.monitoring-code', [
                    'distributor' => $distributor,
                ])->render();
            })
            ->addColumn('name', function ($distributor) {
                return view('page.distributor_documents.partials.monitoring-name', [
                    'distributor' => $distributor,
                    'bupotEmails' => $distributor->bupot_email_list,
                ])->render();
            })
            ->filterColumn('code', function ($query, $keyword) {
                $query->where('distributors.code', 'like', "%{$keyword}%");
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('distributors.name', 'like', "%{$keyword}%")
                        ->orWhere('distributors.code', 'like', "%{$keyword}%")
                        ->orWhere('distributors.bupot_email', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('monthly_progress', function ($distributor) use ($year, $monthNames) {
                return view('page.distributor_documents.partials.monitoring-progress', [
                    'distributor' => $distributor,
                    'year' => $year,
                    'monthNames' => $monthNames,
                    'monthlySummary' => $distributor->getMonthlyDocumentSummary($year),
                ])->render();
            })
            ->addColumn('transfer_running', function ($distributor) use ($year) {
                return view('page.distributor_documents.partials.monitoring-transfer', [
                    'distributor' => $distributor,
                    'year' => $year,
                    'range' => $this->getDistributorTransferYearsRange($distributor),
                ])->render();
            })
            ->addColumn('action', function ($distributor) use ($year) {
                return view('page.distributor_documents.partials.monitoring-actions', [
                    'distributor' => $distributor,
                    'year' => $year,
                ])->render();
            })
            ->rawColumns(['code', 'name', 'monthly_progress', 'transfer_running', 'action'])
            ->make(true);
    }

    /**
     * Helper to compute formatted transfer documents year range string (e.g. '2024-2025' or '2025').
     */
    public function getDistributorTransferYearsRange(Distributor $distributor): ?string
    {
        if ($distributor->transfer_documents_count <= 0) {
            return null;
        }

        $transferYears = $distributor->transferDocuments->map(function ($doc) {
            return $doc->transaction_date
                ? (int) $doc->transaction_date->format('Y')
                : (int) $doc->created_at->format('Y');
        })->filter()->unique()->sort()->values();

        if ($transferYears->isEmpty()) {
            return null;
        }

        $minY = $transferYears->first();
        $maxY = $transferYears->last();

        return $minY === $maxY ? (string) $minY : "{$minY}-{$maxY}";
    }

    /**
     * Get available distributors for dropdown/select options.
     */
    public function getAvailableDistributors(): Collection
    {
        return Distributor::orderBy('code', 'asc')->get(['id', 'code', 'name', 'email', 'bupot_email']);
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

        return $query->reorder();
    }

    /**
     * Build response for running transfer documents.
     */
    public function getTransferDataTable(
        Distributor|int $distributor,
        $transferYear = 'all',
        bool $isPortal = false
    ): JsonResponse {
        $distributorModel = is_numeric($distributor)
            ? $this->findDistributor((int) $distributor)
            : $distributor;

        $query = $this->getTransferDocumentsQuery($distributorModel->id, $transferYear);

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
            ->addColumn('actions', function ($doc) use ($isPortal) {
                return view('page.distributor_documents.partials.document-actions', [
                    'document' => $doc,
                    'isPortal' => $isPortal,
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
            ->orderColumn('transaction_date', function ($query, $order) {
                $query->orderByRaw("COALESCE(transaction_date, created_at) {$order}")
                    ->orderBy('created_at', $order);
            })
            ->orderColumn('formatted_date', function ($query, $order) {
                $query->orderByRaw("COALESCE(transaction_date, created_at) {$order}")
                    ->orderBy('created_at', $order);
            })
            ->orderColumn('title', function ($query, $order) {
                $query->orderBy('title', $order)->orderBy('file_name', $order);
            })
            ->orderColumn('file_display', function ($query, $order) {
                $query->orderBy('title', $order)->orderBy('file_name', $order);
            })
            ->orderColumn('file_size', 'file_size $1')
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

        $addedZipEntries = [];

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

                $timestamp = $att->created_at ? $att->created_at->timestamp : time();
                $randomSuffix = $this->generateRandomStringFromTimestamp($timestamp, 6);

                $pathInfo = pathinfo($att->file_name);
                $baseName = $pathInfo['filename'];
                $extension = ! empty($pathInfo['extension']) ? '.'.$pathInfo['extension'] : '';

                $entryName = "{$baseName}_{$randomSuffix}{$extension}";
                $fullEntryPath = $zipFolder.$entryName;

                // Fallback counter in case files share the exact same timestamp and filename in this folder
                $counter = 1;
                while (isset($addedZipEntries[$fullEntryPath])) {
                    $entryName = "{$baseName}_{$randomSuffix}_{$counter}{$extension}";
                    $fullEntryPath = $zipFolder.$entryName;
                    $counter++;
                }

                $addedZipEntries[$fullEntryPath] = true;
                $zip->addFile($realPath, $fullEntryPath);

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

    /**
     * Generate a unique pseudo-random alphanumeric string derived from a database timestamp.
     *
     * @param int|string $timestamp Unix timestamp or timestamp string from database
     * @param int $length Desired string length (default: 6)
     * @return string
     */
    public function generateRandomStringFromTimestamp(int|string $timestamp, int $length = 6): string
    {
        $alphanumeric = base_convert(substr(md5((string) $timestamp), 0, 10), 16, 36);

        return substr(str_pad($alphanumeric, $length, '0', STR_PAD_RIGHT), 0, $length);
    }

    /**
     * Find distributor by ID.
     */
    public function findDistributor(int $id): Distributor
    {
        return Distributor::findOrFail($id);
    }

    /**
     * Find distributor with customer relation.
     */
    public function findDistributorWithCustomer(int $id): Distributor
    {
        return Distributor::with('customer')->findOrFail($id);
    }

    /**
     * Find attachment by ID.
     */
    public function findAttachment(int $id): DistributorDocumentAttachment
    {
        return DistributorDocumentAttachment::findOrFail($id);
    }

    /**
     * Get aggregated data for internal detail view and partial modal.
     */
    public function getDetailViewData(Distributor|int $distributor, int $year, string $tab = 'monthly', $transferYear = 'all'): array
    {
        $distributorModel = is_numeric($distributor)
            ? $this->findDistributorWithCustomer((int) $distributor)
            : $distributor;

        $documentData = $this->getDetailData($distributorModel, $year, $tab, $transferYear);

        return array_merge([
            'distributor' => $distributorModel,
            'year' => $year,
        ], $documentData);
    }

    /**
     * Render HTML string for public portal distributor content modal.
     */
    public function renderPortalContent(Distributor|int $distributor, int $year, string $tab = 'monthly', $transferYear = 'all'): string
    {
        $distributorModel = is_numeric($distributor)
            ? $this->findDistributor((int) $distributor)
            : $distributor;

        $documentData = $this->getDetailData($distributorModel, $year, $tab, $transferYear);

        return view('auth.partials.portal_distributor_content', array_merge([
            'distributor' => $distributorModel,
            'year' => $year,
        ], $documentData))->render();
    }

    /**
     * Build response payload for successful OTP verification in portal.
     *
     * @return array{success: bool, message: string, distributor: array{id: int, name: string, code: string, bupot_email: ?string}, year: int, html: string}
     */
    public function getPortalVerificationPayload(Distributor $distributor, ?int $year = null): array
    {
        $year = $year ?: now()->year;
        $html = $this->renderPortalContent($distributor, $year, 'monthly', 'all');

        return [
            'success' => true,
            'message' => 'Verifikasi OTP berhasil!',
            'distributor' => [
                'id' => $distributor->id,
                'name' => $distributor->name,
                'code' => $distributor->code,
                'bupot_email' => $distributor->bupot_email,
            ],
            'year' => $year,
            'html' => $html,
        ];
    }

    /**
     * Build response payload for portal dynamic AJAX documents fetch.
     *
     * @return array{success: bool, year: int, tab: string, html: string}
     */
    public function getPortalDocumentData(Distributor|int $distributor, int $year, string $tab = 'monthly', $transferYear = 'all'): array
    {
        $html = $this->renderPortalContent($distributor, $year, $tab, $transferYear);

        return [
            'success' => true,
            'year' => $year,
            'tab' => $tab,
            'html' => $html,
        ];
    }

    /**
     * Process upload request and return structured metadata.
     *
     * @return array{attachment: DistributorDocumentAttachment, distributor_id: int, year: int, tab: string, is_transfer: bool, message: string}
     */
    public function uploadDocumentFromRequest(array $validatedData, UploadedFile $file, ?int $uploadedBy = null): array
    {
        $distributor = $this->findDistributor((int) $validatedData['distributor_id']);
        $attachment = $this->uploadDocument($distributor, $validatedData, $file, $uploadedBy);

        $isTransfer = $attachment->doc_type === 'transfer';
        $year = $attachment->year ?? (int) date('Y');

        return [
            'attachment' => $attachment,
            'distributor_id' => $distributor->id,
            'year' => $year,
            'tab' => $isTransfer ? 'transfer' : 'monthly',
            'is_transfer' => $isTransfer,
            'message' => 'Dokumen berhasil diunggah.',
        ];
    }

    /**
     * Delete attachment by ID and return metadata for redirection / response.
     *
     * @return array{distributor_id: int, year: int, tab: string, message: string}
     */
    public function deleteAttachmentById(int $attachmentId): array
    {
        $attachment = DistributorDocumentAttachment::with('document')->findOrFail($attachmentId);
        $distributorId = $attachment->distributor_id;
        $year = $attachment->year ?? (int) date('Y');
        $docType = $attachment->doc_type;

        $this->deleteAttachment($attachment);

        return [
            'distributor_id' => $distributorId,
            'year' => $year,
            'tab' => $docType === 'transfer' ? 'transfer' : 'monthly',
            'message' => 'Dokumen berhasil dihapus.',
        ];
    }

    /**
     * Generate preview response by attachment ID.
     */
    public function previewAttachment(int $id): BinaryFileResponse
    {
        $attachment = $this->findAttachment($id);

        return $this->previewResponse($attachment);
    }

    /**
     * Generate download response by attachment ID.
     */
    public function downloadAttachment(int $id, string $via, ?Request $request = null): BinaryFileResponse
    {
        $attachment = $this->findAttachment($id);

        return $this->downloadResponse($attachment, $via, $request);
    }

    /**
     * Generate zip download response by distributor ID.
     */
    public function downloadZipByDistributorId(int $distributorId, int $year, string $via, ?Request $request = null): BinaryFileResponse
    {
        $distributor = $this->findDistributor($distributorId);

        return $this->downloadZipResponse($distributor, $year, $via, $request);
    }

    /**
     * Register distributor for a specific year and return result message.
     *
     * @return array{distributor: Distributor, year: int, message: string}
     */
    public function registerDistributor(
        int $distributorId,
        int $year,
        bool $withUpload = false,
        ?array $uploadData = null,
        ?UploadedFile $file = null,
        ?int $uploadedBy = null
    ): array {
        $distributor = $this->findDistributor($distributorId);

        $this->registerDistributorToYear(
            $distributor,
            $year,
            $withUpload,
            $uploadData,
            $file,
            $uploadedBy
        );

        $message = $withUpload
            ? "Distributor '{$distributor->name}' berhasil ditambahkan ke list tahun {$year} dan dokumen berhasil diunggah."
            : "Distributor '{$distributor->name}' berhasil ditambahkan ke list dokumen tahun {$year}.";

        return [
            'distributor' => $distributor,
            'year' => $year,
            'message' => $message,
        ];
    }
}
