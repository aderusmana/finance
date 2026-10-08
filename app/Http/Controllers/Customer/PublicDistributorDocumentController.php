<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\DistributorOtpMail;
use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocumentAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PublicDistributorDocumentController extends Controller
{
    /**
     * Request OTP for external distributor using code and registered bupot_email.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        // Anti-bot honeypot: reject if hidden field is filled
        if ($request->filled('website_hp')) {
            return response()->json(['message' => 'Permintaan tidak valid.'], 422);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
        ], [
            'code.required' => 'Kode Distributor wajib diisi.',
            'email.required' => 'Email terdaftar wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $code = strtoupper(trim($validated['code']));
        $inputEmail = strtolower(trim($validated['email']));

        $distributor = Distributor::where('code', $code)->first();

        // Security best practice: don't reveal whether code or email was wrong specifically
        if (! $distributor) {
            return response()->json([
                'message' => 'Kode Distributor atau Email tidak sesuai dengan data terdaftar.',
            ], 422);
        }

        // Validate strictly against bupot_email list only (per requirement)
        $allowedBupotEmails = array_map('strtolower', $distributor->bupot_email_list);

        if (empty($allowedBupotEmails)) {
            return response()->json([
                'message' => 'Distributor ini belum memiliki Email Bukti Potong (bupot_email) terdaftar di sistem. Silakan hubungi Finance.',
            ], 422);
        }

        if (! in_array($inputEmail, $allowedBupotEmails, true)) {
            return response()->json([
                'message' => 'Kode Distributor atau Email tidak sesuai dengan data terdaftar.',
            ], 422);
        }

        // Check Cooldown (60 seconds) per IP & distributor
        $cooldownKey = "distributor_otp_cd_{$distributor->id}_".md5($request->ip());
        if (Cache::has($cooldownKey)) {
            $secondsRemaining = Cache::get($cooldownKey.'_time', 60);

            return response()->json([
                'message' => "Mohon tunggu {$secondsRemaining} detik sebelum meminta kode OTP kembali.",
                'cooldown' => $secondsRemaining,
            ], 429);
        }

        // Generate 6-digit cryptographically secure OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in Cache for 5 minutes (300 seconds)
        $otpCacheKey = "distributor_otp_{$distributor->id}";
        Cache::put($otpCacheKey, [
            'otp_hash' => Hash::make($otp),
            'email' => $inputEmail,
            'attempts' => 0,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(5));

        // Set cooldown for 60 seconds
        Cache::put($cooldownKey, true, now()->addSeconds(60));
        Cache::put($cooldownKey.'_time', 60, now()->addSeconds(60));

        // Send OTP Mail
        try {
            Mail::to($inputEmail)->send(new DistributorOtpMail($distributor, $otp, 5));
        } catch (\Throwable $e) {
            Log::error('Distributor OTP mail send failed: '.$e->getMessage(), [
                'distributor_id' => $distributor->id,
                'email' => $inputEmail,
            ]);
        }

        // Mask email for user privacy
        $parts = explode('@', $inputEmail);
        $userPart = $parts[0] ?? '';
        $domainPart = $parts[1] ?? '';
        $maskedUser = (strlen($userPart) > 2)
            ? substr($userPart, 0, 1).str_repeat('*', min(4, strlen($userPart) - 1))
            : substr($userPart, 0, 1).'***';
        $maskedEmail = $maskedUser.'@'.$domainPart;

        return response()->json([
            'success' => true,
            'message' => "Kode OTP telah dikirimkan ke {$maskedEmail}. Silakan periksa kotak masuk atau spam.",
            'masked_email' => $maskedEmail,
            'cooldown' => 60,
        ]);
    }

    /**
     * Verify OTP and return document data HTML for modal display.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        // Anti-bot honeypot
        if ($request->filled('website_hp')) {
            return response()->json(['message' => 'Permintaan tidak valid.'], 422);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'otp' => ['required', 'digits:6'],
            'year' => ['nullable', 'integer', 'digits:4'],
        ], [
            'code.required' => 'Kode Distributor wajib diisi.',
            'email.required' => 'Email terdaftar wajib diisi.',
            'otp.required' => 'Kode OTP 6-digit wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        $code = strtoupper(trim($validated['code']));
        $inputEmail = strtolower(trim($validated['email']));
        $otp = trim($validated['otp']);
        $year = (int) ($validated['year'] ?? now()->year);

        $distributor = Distributor::where('code', $code)->first();
        if (! $distributor) {
            return response()->json(['message' => 'Data distributor tidak ditemukan.'], 422);
        }

        $otpCacheKey = "distributor_otp_{$distributor->id}";
        $cachedOtpData = Cache::get($otpCacheKey);

        if (! $cachedOtpData) {
            return response()->json([
                'message' => 'Kode OTP telah kadaluarsa atau belum diminta. Silakan minta kode OTP baru.',
            ], 422);
        }

        if ($cachedOtpData['email'] !== $inputEmail) {
            return response()->json(['message' => 'Email tidak sesuai dengan permintaan OTP.'], 422);
        }

        // Limit brute force attempts (max 5 failed attempts per OTP code)
        if (($cachedOtpData['attempts'] ?? 0) >= 5) {
            Cache::forget($otpCacheKey);

            return response()->json([
                'message' => 'Terlalu banyak percobaan kode OTP salah. Silakan minta kode OTP baru.',
            ], 422);
        }

        // Verify OTP Hash
        if (! Hash::check($otp, $cachedOtpData['otp_hash'])) {
            $cachedOtpData['attempts'] = ($cachedOtpData['attempts'] ?? 0) + 1;
            $remaining = 5 - $cachedOtpData['attempts'];
            Cache::put($otpCacheKey, $cachedOtpData, now()->addMinutes(5));

            return response()->json([
                'message' => "Kode OTP salah. Sisa kesempatan: {$remaining} kali.",
            ], 422);
        }

        // OTP Verified successfully! Invalidate one-time OTP from cache
        Cache::forget($otpCacheKey);

        // Store authenticated session for distributor portal (valid for 30 minutes)
        session([
            'distributor_portal_verified_id' => $distributor->id,
            'distributor_portal_verified_code' => $distributor->code,
            'distributor_portal_verified_at' => now()->timestamp,
            'distributor_portal_expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        // Fetch documents for the initial view
        $documentData = $this->fetchDistributorDocumentData($distributor, $year, 'monthly', 'all');

        $html = view('auth.partials.portal_distributor_content', array_merge([
            'distributor' => $distributor,
            'year' => $year,
        ], $documentData))->render();

        return response()->json([
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
        ]);
    }

    /**
     * Fetch documents dynamically via AJAX (e.g. when changing year or tab inside modal).
     */
    public function getDocuments(Request $request): JsonResponse
    {
        $distributorId = (int) $request->get('distributor_id');
        $year = (int) ($request->get('year') ?: now()->year);
        $tab = $request->get('tab', 'monthly');
        $transferYear = $request->get('transfer_year', 'all');

        // Verify active portal session
        $this->authorizePortalAccess($distributorId);

        $distributor = Distributor::findOrFail($distributorId);
        $documentData = $this->fetchDistributorDocumentData($distributor, $year, $tab, $transferYear);

        $html = view('auth.partials.portal_distributor_content', array_merge([
            'distributor' => $distributor,
            'year' => $year,
        ], $documentData))->render();

        return response()->json([
            'success' => true,
            'year' => $year,
            'tab' => $tab,
            'html' => $html,
        ]);
    }

    /**
     * Preview single file PDF with session ownership validation.
     */
    public function previewFile($id)
    {
        $document = DistributorDocumentAttachment::findOrFail($id);

        $this->authorizePortalAccess($document->distributor_id);

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => $document->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$document->file_name.'"',
        ]);
    }

    /**
     * Download single file while recording audit trail.
     */
    public function downloadFile(Request $request, $id)
    {
        $document = DistributorDocumentAttachment::findOrFail($id);

        $this->authorizePortalAccess($document->distributor_id);

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Record download audit trail
        DB::table('distributor_document_downloads')->insert([
            'distributor_document_attachment_id' => $document->id,
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
     * Bundling download documents to zip file with session validation.
     */
    public function downloadAllZip(Request $request)
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        $distributorId = (int) $request->distributor_id;
        $this->authorizePortalAccess($distributorId);

        $distributor = Distributor::findOrFail($distributorId);
        $year = (int) $request->year;

        // Get monthly attachments for selected year + all running transfer files
        $documents = DistributorDocumentAttachment::where('distributor_id', $distributor->id)
            ->whereNotNull('file_path')
            ->where(function ($query) use ($year) {
                $query->where(function ($q) use ($year) {
                    $q->whereIn('doc_type', ['bupot', 'top_insentif'])
                        ->whereHas('document', function ($docQ) use ($year) {
                            $docQ->where('year', $year);
                        });
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
                        'distributor_document_attachment_id' => $doc->id,
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

    /**
     * Terminate portal guest session.
     */
    public function portalLogout(Request $request): JsonResponse
    {
        session()->forget([
            'distributor_portal_verified_id',
            'distributor_portal_verified_code',
            'distributor_portal_verified_at',
            'distributor_portal_expires_at',
        ]);

        return response()->json(['success' => true, 'message' => 'Sesi portal telah ditutup.']);
    }

    /**
     * Helper to verify if request is authorized to access distributor documents.
     */
    protected function authorizePortalAccess(int $distributorId): void
    {
        if (auth()->check()) {
            return;
        }

        $sessionDistributorId = session('distributor_portal_verified_id');
        $expiresAt = session('distributor_portal_expires_at');

        if (! $sessionDistributorId || (int) $sessionDistributorId !== $distributorId || now()->timestamp > (int) $expiresAt) {
            abort(403, 'Akses tidak diizinkan atau sesi telah berakhir. Silakan verifikasi OTP terlebih dahulu.');
        }
    }

    /**
     * Helper to fetch organized document data matching detail view structure.
     */
    protected function fetchDistributorDocumentData(Distributor $distributor, int $year, string $tab = 'monthly', $transferYear = 'all'): array
    {
        // Monthly docs for selected year
        $monthlyDocs = DistributorDocumentAttachment::where('distributor_id', $distributor->id)
            ->whereNotNull('file_path')
            ->whereIn('doc_type', ['bupot', 'top_insentif'])
            ->whereHas('document', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get()
            ->groupBy(['month', 'doc_type']);

        // Transfer explanation documents (running multi-year)
        $transferDocsQuery = DistributorDocumentAttachment::runningTransfers($distributor->id);

        if ($transferYear && $transferYear !== 'all') {
            $transferYearInt = (int) $transferYear;
            $transferDocsQuery->where(function ($q) use ($transferYearInt) {
                $q->whereYear('transaction_date', $transferYearInt)
                    ->orWhere(function ($subQ) use ($transferYearInt) {
                        $subQ->whereNull('transaction_date')
                            ->whereHas('document', function ($docQ) use ($transferYearInt) {
                                $docQ->where('year', $transferYearInt);
                            });
                    });
            });
        }

        $transferDocs = $transferDocsQuery->get();

        // Available years for transfer filter
        $availableTransferYears = DistributorDocumentAttachment::where('distributor_id', $distributor->id)
            ->where('doc_type', 'transfer')
            ->whereNotNull('file_path')
            ->selectRaw('DISTINCT COALESCE(YEAR(transaction_date), (SELECT year FROM distributor_documents WHERE id = distributor_document_attachments.distributor_document_id)) as doc_year')
            ->pluck('doc_year')
            ->filter()
            ->sortDesc()
            ->values();

        return [
            'monthlyDocs' => $monthlyDocs,
            'transferDocs' => $transferDocs,
            'tab' => $tab,
            'transferYear' => $transferYear,
            'availableTransferYears' => $availableTransferYears,
        ];
    }
}
