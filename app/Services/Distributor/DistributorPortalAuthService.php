<?php

namespace App\Services\Distributor;

use App\Mail\DistributorOtpMail;
use App\Models\Customer\Distributor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DistributorPortalAuthService
{
    /**
     * Request OTP for external distributor using code and registered bupot_email.
     *
     * @return array{success: bool, message: string, masked_email: string, cooldown: int}
     */
    public function requestOtp(string $code, string $email, string $ip): array
    {
        $code = strtoupper(trim($code));
        $inputEmail = strtolower(trim($email));

        $distributor = Distributor::where('code', $code)->first();

        // Security best practice: don't reveal whether code or email was wrong
        if (! $distributor) {
            throw ValidationException::withMessages([
                'auth' => 'Kode Distributor atau Email tidak sesuai dengan data terdaftar.',
            ]);
        }

        // Validate strictly against bupot_email list only (per requirement)
        $allowedBupotEmails = array_map('strtolower', $distributor->bupot_email_list);

        if (empty($allowedBupotEmails)) {
            throw ValidationException::withMessages([
                'auth' => 'Distributor ini belum memiliki Email Bukti Potong (bupot_email) terdaftar di sistem. Silakan hubungi Finance.',
            ]);
        }

        if (! in_array($inputEmail, $allowedBupotEmails, true)) {
            throw ValidationException::withMessages([
                'auth' => 'Kode Distributor atau Email tidak sesuai dengan data terdaftar.',
            ]);
        }

        // Check Cooldown (60 seconds) per IP & distributor
        $cooldownKey = "distributor_otp_cd_{$distributor->id}_".md5($ip);
        if (Cache::has($cooldownKey)) {
            $secondsRemaining = Cache::get($cooldownKey.'_time', 60);

            abort(response()->json([
                'message' => "Mohon tunggu {$secondsRemaining} detik sebelum meminta kode OTP kembali.",
                'cooldown' => $secondsRemaining,
            ], 429));
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
        $maskedEmail = $this->maskEmail($inputEmail);

        return [
            'success' => true,
            'message' => "Kode OTP telah dikirimkan ke {$maskedEmail}. Silakan periksa kotak masuk atau spam.",
            'masked_email' => $maskedEmail,
            'cooldown' => 60,
        ];
    }

    /**
     * Verify OTP and establish verified distributor portal session.
     */
    public function verifyOtp(string $code, string $email, string $otp): Distributor
    {
        $code = strtoupper(trim($code));
        $inputEmail = strtolower(trim($email));
        $otp = trim($otp);

        $distributor = Distributor::where('code', $code)->first();
        if (! $distributor) {
            throw ValidationException::withMessages([
                'auth' => 'Data distributor tidak ditemukan.',
            ]);
        }

        $otpCacheKey = "distributor_otp_{$distributor->id}";
        $cachedOtpData = Cache::get($otpCacheKey);

        if (! $cachedOtpData) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP telah kadaluarsa atau belum diminta. Silakan minta kode OTP baru.',
            ]);
        }

        if ($cachedOtpData['email'] !== $inputEmail) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak sesuai dengan permintaan OTP.',
            ]);
        }

        // Limit brute force attempts (max 5 failed attempts per OTP code)
        if (($cachedOtpData['attempts'] ?? 0) >= 5) {
            Cache::forget($otpCacheKey);

            throw ValidationException::withMessages([
                'otp' => 'Terlalu banyak percobaan kode OTP salah. Silakan minta kode OTP baru.',
            ]);
        }

        // Verify OTP Hash
        if (! Hash::check($otp, $cachedOtpData['otp_hash'])) {
            $cachedOtpData['attempts'] = ($cachedOtpData['attempts'] ?? 0) + 1;
            $remaining = 5 - $cachedOtpData['attempts'];
            Cache::put($otpCacheKey, $cachedOtpData, now()->addMinutes(5));

            throw ValidationException::withMessages([
                'otp' => "Kode OTP salah. Sisa kesempatan: {$remaining} kali.",
            ]);
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

        return $distributor;
    }

    /**
     * Enforce access authorization for distributor portal.
     */
    public function authorizePortalAccess(int $distributorId): void
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
     * Terminate distributor portal session.
     */
    public function logout(): void
    {
        session()->forget([
            'distributor_portal_verified_id',
            'distributor_portal_verified_code',
            'distributor_portal_verified_at',
            'distributor_portal_expires_at',
        ]);
    }

    /**
     * Mask email address for privacy.
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $userPart = $parts[0] ?? '';
        $domainPart = $parts[1] ?? '';
        $maskedUser = (strlen($userPart) > 2)
            ? substr($userPart, 0, 1).str_repeat('*', min(4, strlen($userPart) - 1))
            : substr($userPart, 0, 1).'***';

        return $maskedUser.'@'.$domainPart;
    }
}
