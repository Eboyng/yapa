<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'amount',
        'currency',
        'status',
        'expires_at',
        'redeemed_at',
        'redeemed_by',
        'created_by',
        'description',
        'metadata',
        'batch_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Voucher statuses.
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_REDEEMED = 'redeemed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Currency types.
     */
    const CURRENCY_NGN = 'NGN';
    const CURRENCY_CREDITS = 'CREDITS';

    /**
     * Get the user who redeemed the voucher.
     */
    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    /**
     * Get the admin who created the voucher.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if voucher is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if voucher is redeemed.
     */
    public function isRedeemed(): bool
    {
        return $this->status === self::STATUS_REDEEMED;
    }

    /**
     * Check if voucher is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status !== self::STATUS_REDEEMED;
    }

    /**
     * Check if voucher can be redeemed.
     */
    public function canBeRedeemed(): bool
    {
        return $this->isActive() && !$this->isRedeemed() && !$this->isExpired();
    }

    /**
     * Redeem the voucher.
     */
    public function redeem(User $user): bool
    {
        if (!$this->canBeRedeemed()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REDEEMED,
            'redeemed_at' => now(),
            'redeemed_by' => $user->id,
        ]);

        return true;
    }

    /**
     * Generate a unique voucher code.
     */
    public static function generateCode(): string
    {
        do {
            $code = 'VCH-' . strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create multiple vouchers at once.
     */
    public static function createBatch(
        int $count,
        float $amount,
        string $currency = self::CURRENCY_NGN,
        ?Carbon $expiresAt = null,
        ?string $description = null,
        ?int $createdBy = null
    ): array {
        $vouchers = [];
        $batchId = Str::uuid();

        for ($i = 0; $i < $count; $i++) {
            $vouchers[] = static::create([
                'code' => static::generateCode(),
                'amount' => $amount,
                'currency' => $currency,
                'status' => self::STATUS_ACTIVE,
                'expires_at' => $expiresAt,
                'description' => $description,
                'created_by' => $createdBy,
                'batch_id' => $batchId,
                'metadata' => [
                    'batch_size' => $count,
                    'batch_created_at' => now()->toISOString(),
                ],
            ]);
        }

        return $vouchers;
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->currency === self::CURRENCY_CREDITS) {
            return number_format($this->amount, 0) . ' credits';
        }
        
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_REDEEMED => 'primary',
            self::STATUS_EXPIRED => 'warning',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope for active vouchers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for redeemed vouchers.
     */
    public function scopeRedeemed($query)
    {
        return $query->where('status', self::STATUS_REDEEMED);
    }

    /**
     * Scope for expired vouchers.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('status', '!=', self::STATUS_REDEEMED);
    }

    /**
     * Scope by currency.
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope by batch.
     */
    public function scopeByBatch($query, string $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            if (empty($voucher->code)) {
                $voucher->code = static::generateCode();
            }
            if (is_null($voucher->status)) {
                $voucher->status = self::STATUS_ACTIVE;
            }
            if (is_null($voucher->currency)) {
                $voucher->currency = self::CURRENCY_NGN;
            }
        });
    }
}