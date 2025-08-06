<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'otp_code',
        'context',
        'user_id',
        'attempts',
        'expires_at',
        'verified',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * Generate and store OTP.
     */
    public static function generate(string $identifier, string $context, ?int $userId = null, int $expiryMinutes = 5): array
    {
        // Force cleanup of any existing OTP for this identifier and context (including verified ones)
        self::where('identifier', $identifier)
            ->where('context', $context)
            ->forceDelete();
            
        // Also cleanup any expired OTPs to keep the table clean
        self::cleanupExpired();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $otpRecord = self::create([
            'identifier' => $identifier,
            'otp_code' => Hash::make($otp),
            'context' => $context,
            'user_id' => $userId,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
        ]);

        return [
            'otp' => $otp,
            'record' => $otpRecord,
        ];
    }

    /**
     * Verify OTP code.
     */
    public static function verify(string $identifier, string $otpCode, string $context): array
    {
        $otpRecord = self::where('identifier', $identifier)
            ->where('context', $context)
            ->where('verified', false)
            ->first();

        if (!$otpRecord) {
            return [
                'success' => false,
                'message' => 'OTP not found or already verified.',
            ];
        }

        // Check if OTP has expired
        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->delete();
            return [
                'success' => false,
                'message' => 'OTP has expired.',
            ];
        }

        // Check maximum attempts
        if ($otpRecord->attempts >= 3) {
            $otpRecord->delete();
            return [
                'success' => false,
                'message' => 'Maximum OTP attempts exceeded.',
            ];
        }

        // Increment attempts
        $otpRecord->increment('attempts');

        // Verify OTP
        if (!Hash::check($otpCode, $otpRecord->otp_code)) {
            return [
                'success' => false,
                'message' => 'Invalid OTP code.',
            ];
        }

        // Mark as verified
        $otpRecord->update([
            'verified' => true,
            'verified_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'message' => 'OTP verified successfully.',
            'record' => $otpRecord,
        ];
    }

    /**
     * Clean up expired OTPs.
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }

    /**
     * Check if can resend OTP (rate limiting).
     */
    public static function canResend(string $identifier, string $context, int $cooldownMinutes = 1): bool
    {
        $lastOtp = self::where('identifier', $identifier)
            ->where('context', $context)
            ->latest()
            ->first();

        if (!$lastOtp) {
            return true;
        }

        return $lastOtp->created_at->addMinutes($cooldownMinutes)->isPast();
    }

    /**
     * Force cleanup all OTP records for a specific identifier and context.
     */
    public static function forceCleanup(string $identifier, string $context): int
    {
        return self::where('identifier', $identifier)
            ->where('context', $context)
            ->forceDelete();
    }

    /**
     * Relationship with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}