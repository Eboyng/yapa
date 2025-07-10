<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class PendingUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'whatsapp_number',
        'password',
        'otp_code',
        'otp_attempts',
        'otp_expires_at',
        'resend_attempts',
        'last_resend_at',
        'failure_reason',
        'expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'last_resend_at' => 'datetime',
        'expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pendingUser) {
            if (empty($pendingUser->expires_at)) {
                $pendingUser->expires_at = Carbon::now()->addHours(24);
            }
        });
    }

    /**
     * Check if the pending user has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP has expired.
     */
    public function hasOtpExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    /**
     * Check if max OTP attempts reached.
     */
    public function hasMaxOtpAttempts(): bool
    {
        return $this->otp_attempts >= 3;
    }

    /**
     * Check if can resend OTP (rate limiting).
     */
    public function canResendOtp(): bool
    {
        if ($this->resend_attempts >= 3) {
            return false;
        }

        if ($this->last_resend_at && $this->last_resend_at->diffInMinutes(Carbon::now()) < 1) {
            return false;
        }

        return true;
    }

    /**
     * Generate and set OTP code.
     */
    public function generateOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        return $otp;
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(string $otp): bool
    {
        if ($this->hasOtpExpired() || $this->hasMaxOtpAttempts()) {
            return false;
        }

        $this->increment('otp_attempts');

        return Hash::check($otp, $this->otp_code);
    }

    /**
     * Convert to User model.
     */
    public function convertToUser(): User
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsapp_number,
            'password' => $this->password,
            'whatsapp_verified_at' => Carbon::now(),
        ]);
        
        // Wallets are automatically created with default balances via User::boot()
        // No need to manually create wallets or deposit credits

        // Delete the pending user record
        $this->delete();

        return $user;
    }

    /**
     * Scope to get non-expired pending users.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope to get expired pending users.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }
}