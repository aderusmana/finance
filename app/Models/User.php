<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Master\Department;
use App\Models\Master\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nik',
        'username',
        'email',
        'name',
        'no_telepon',
        'avatar',
        'department_id',
        'position_id',
        'atasan_nik',
        'password',
        'status',
        'failed_login_attempts',
        'locked_until',
        'last_failed_login_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'locked_until' => 'datetime',
            'last_failed_login_at' => 'datetime',
            'last_login_at' => 'datetime',
            'failed_login_attempts' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user account is currently locked.
     */
    public function isLocked(): bool
    {
        if ($this->status === 'locked') {
            if ($this->locked_until && $this->locked_until->isFuture()) {
                return true;
            }
            if (!$this->locked_until) {
                return true;
            }
        }

        if ($this->locked_until && $this->locked_until->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Lock the user account for a specified duration in minutes.
     */
    public function lockAccount(int $minutes = 15): void
    {
        $this->update([
            'status' => 'locked',
            'locked_until' => now()->addMinutes($minutes),
            'last_failed_login_at' => now(),
        ]);
    }

    /**
     * Unlock the user account and reset failed login attempts.
     */
    public function unlockAccount(): void
    {
        $this->update([
            'status' => 'active',
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Record a failed login attempt and lock if threshold is reached.
     * Returns remaining attempts (0 if locked).
     */
    public function recordFailedLogin(int $maxAttempts = 5, int $lockoutMinutes = 15): int
    {
        $newAttempts = ((int) ($this->failed_login_attempts ?? 0)) + 1;

        if ($newAttempts >= $maxAttempts) {
            $this->update([
                'failed_login_attempts' => $newAttempts,
                'status' => 'locked',
                'locked_until' => now()->addMinutes($lockoutMinutes),
                'last_failed_login_at' => now(),
            ]);

            return 0;
        }

        $this->update([
            'failed_login_attempts' => $newAttempts,
            'last_failed_login_at' => now(),
        ]);

        return max(0, $maxAttempts - $newAttempts);
    }

    /**
     * Reset lockout state and record successful login timestamp & IP.
     */
    public function recordSuccessfulLogin(?string $ip = null): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Get remaining lockout seconds if locked.
     */
    public function lockoutSecondsRemaining(): int
    {
        if ($this->locked_until && $this->locked_until->isFuture()) {
            return now()->diffInSeconds($this->locked_until);
        }

        return 0;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function atasan()
    {
        return $this->belongsTo(User::class, 'atasan_nik', 'nik');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
