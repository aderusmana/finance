<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $nik = $this->string('nik')->toString();
        $user = User::where('nik', $nik)->first();

        $maxAttempts = (int) env('AUTH_LOCKOUT_MAX_ATTEMPTS', 5);
        $lockoutMinutes = (int) env('AUTH_LOCKOUT_MINUTES', 5);

        // 1. First priority: Check if user account exists and inspect status / lock
        if ($user) {
            // Check Inactive / Suspended status
            if ($user->status === 'inactive' || $user->status === 'suspended') {
                throw ValidationException::withMessages([
                    'nik' => 'Akun Anda berstatus non-aktif. Silakan hubungi Administrator.',
                ]);
            }

            // Check if user is locked
            if ($user->isLocked()) {
                if ($user->locked_until && $user->locked_until->isFuture()) {
                    $seconds = now()->diffInSeconds($user->locked_until);
                    session()->flash('lockout_seconds', $seconds);
                    $minutes = ceil($seconds / 60);

                    // Clear IP rate limiter so it doesn't conflict with account lock
                    RateLimiter::clear($this->throttleKey());

                    $timeText = $seconds >= 60 
                        ? "{$minutes} menit ({$seconds} detik)" 
                        : "{$seconds} detik";

                    throw ValidationException::withMessages([
                        'nik' => "Akun Anda sedang terkunci sementara karena {$maxAttempts} kali percobaan login yang gagal. Silakan coba lagi dalam {$timeText} atau hubungi Administrator.",
                    ]);
                }

                // If locked by admin without locked_until
                if ($user->status === 'locked' && !$user->locked_until) {
                    throw ValidationException::withMessages([
                        'nik' => 'Akun Anda terkunci oleh Administrator. Silakan hubungi Administrator untuk membuka kunci akun.',
                    ]);
                }

                // Auto-unlock if lockout duration has expired
                if ($user->locked_until && $user->locked_until->isPast()) {
                    $user->unlockAccount();
                    RateLimiter::clear($this->throttleKey());

                    try {
                        activity()
                            ->causedBy($user)
                            ->performedOn($user)
                            ->event('auth')
                            ->log('Account auto-unlocked after lockout duration expired.');
                    } catch (\Throwable $e) {
                        // Ignore logging failure
                    }
                }
            }
        }

        // 2. IP & User-based short-term Rate Limiter (only applies if account is not locked)
        $this->ensureIsNotRateLimited();

        // 3. Attempt Authentication
        if (! Auth::attempt($this->only('nik', 'password'), $this->boolean('remember'))) {
            if ($user) {
                $remaining = $user->recordFailedLogin(maxAttempts: $maxAttempts, lockoutMinutes: $lockoutMinutes);

                if ($remaining === 0) {
                    event(new Lockout($this));
                    
                    // Clear IP rate limiter so only the account lockout timer controls the cooldown
                    RateLimiter::clear($this->throttleKey());

                    $lockoutSeconds = $lockoutMinutes * 60;
                    session()->flash('lockout_seconds', $lockoutSeconds);

                    try {
                        activity()
                            ->causedBy($user)
                            ->performedOn($user)
                            ->event('auth')
                            ->withProperties([
                                'ip' => $this->ip(),
                                'user_agent' => $this->userAgent(),
                                'failed_attempts' => $user->failed_login_attempts,
                            ])
                            ->log("User account locked due to {$maxAttempts} consecutive failed login attempts.");
                    } catch (\Throwable $e) {
                        // Ignore logging failure
                    }

                    throw ValidationException::withMessages([
                        'nik' => "Akun Anda telah dikunci sementara selama {$lockoutMinutes} menit ({$lockoutSeconds} detik) karena {$maxAttempts} kali percobaan login yang gagal. Silakan hubungi Administrator jika membutuhkan bantuan.",
                    ]);
                }

                // Still has attempts remaining: record hit in IP rate limiter
                RateLimiter::hit($this->throttleKey(), 60);

                try {
                    activity()
                        ->causedBy($user)
                        ->performedOn($user)
                        ->event('auth')
                        ->withProperties([
                            'ip' => $this->ip(),
                            'user_agent' => $this->userAgent(),
                            'attempt' => $user->failed_login_attempts,
                            'remaining' => $remaining,
                        ])
                        ->log("Failed login attempt ({$user->failed_login_attempts}/{$maxAttempts}).");
                } catch (\Throwable $e) {
                    // Ignore logging failure
                }

                throw ValidationException::withMessages([
                    'nik' => "NIK atau password salah. Sisa kesempatan login: {$remaining} kali sebelum akun terkunci.",
                ]);
            }

            // User does not exist in DB
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'nik' => trans('auth.failed'),
            ]);
        }

        // 4. Successful Authentication: Reset counters & record metadata
        RateLimiter::clear($this->throttleKey());

        if ($user) {
            $user->recordSuccessfulLogin($this->ip());

            try {
                activity()
                    ->causedBy($user)
                    ->performedOn($user)
                    ->event('auth')
                    ->withProperties([
                        'ip' => $this->ip(),
                        'user_agent' => $this->userAgent(),
                    ])
                    ->log('User logged in successfully.');
            } catch (\Throwable $e) {
                // Ignore logging failure
            }
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        session()->flash('lockout_seconds', $seconds);

        throw ValidationException::withMessages([
            'nik' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('nik')).'|'.$this->ip());
    }
}
