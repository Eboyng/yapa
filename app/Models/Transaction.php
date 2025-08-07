<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'wallet_id',
        'reference',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'metadata',
        'status',
        'payment_method',
        'payment_reference',
        'gateway_response',
        'retry_count',
        'failed_at',
        'completed_at',
        'processed_at',
        'admin_notes',
        'related_id',
        'source',
        'parent_transaction_id',
        'retry_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'gateway_response' => 'array',
        'retry_count' => 'integer',
        'failed_at' => 'datetime',
        'completed_at' => 'datetime',
        'processed_at' => 'datetime',
        'retry_until' => 'datetime',
    ];

    /**
     * Transaction types.
     */
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';
    const TYPE_REFUND = 'refund';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_NAIRA = 'naira';
    const TYPE_EARNINGS = 'earnings';

    /**
     * Transaction categories.
     */
    const CATEGORY_CREDIT_PURCHASE = 'credit_purchase';
    const CATEGORY_NAIRA_FUNDING = 'naira_funding';
    const CATEGORY_WHATSAPP_MESSAGE = 'whatsapp_message';
    const CATEGORY_SMS_MESSAGE = 'sms_message';
    const CATEGORY_REFUND = 'refund';
    const CATEGORY_EARNINGS = 'earnings';
    const CATEGORY_WITHDRAWAL = 'withdrawal';
    const CATEGORY_BONUS = 'bonus';
    const CATEGORY_PENALTY = 'penalty';
    const CATEGORY_WHATSAPP_CHANGE_FEE = 'whatsapp_change_fee';
    const CATEGORY_BATCH_JOIN = 'batch_join';
    const CATEGORY_AD_EARNING = 'ad_earning';
    const CATEGORY_MANUAL_ADJUSTMENT = 'manual_adjustment';
    const CATEGORY_CHANNEL_AD_ESCROW = 'channel_ad_escrow';
    const CATEGORY_CHANNEL_AD_BOOKING = 'channel_ad_booking';
    const CATEGORY_CHANNEL_AD_PAYMENT = 'channel_ad_payment';
    const CATEGORY_CHANNEL_SALE_ESCROW = 'channel_sale_escrow';
    const CATEGORY_CHANNEL_SALE_PAYMENT = 'channel_sale_payment';
    const CATEGORY_REFERRAL_REWARD = 'referral_reward';
    const CATEGORY_BATCH_SHARE_REWARD = 'batch_share_reward';
    const CATEGORY_CONTACT_DOWNLOAD = 'contact_download';
    const CATEGORY_VOUCHER_REDEMPTION = 'voucher_redemption';

    /**
     * Transaction statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Payment methods.
     */
    const PAYMENT_METHOD_PAYSTACK = 'paystack';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_METHOD_WALLET = 'wallet';
    const PAYMENT_METHOD_SYSTEM = 'system';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->reference)) {
                $transaction->reference = static::generateReference();
            }
        });
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the parent transaction (for refunds).
     */
    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    /**
     * Get child transactions (refunds of this transaction).
     */
    public function childTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id');
    }

    /**
     * Get the ad task associated with this transaction.
     */
    public function adTask(): BelongsTo
    {
        return $this->belongsTo(AdTask::class, 'related_id');
    }

    /**
     * Generate unique transaction reference.
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'TXN_' . strtoupper(Str::random(12));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is successful.
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CONFIRMED]);
    }

    /**
     * Check if transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if transaction is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if transaction can be retried.
     */
    public function canRetry(): bool
    {
        return $this->isFailed() && 
               $this->retry_count < 3 && 
               $this->retry_until && 
               $this->retry_until->isFuture();
    }

    /**
     * Check if transaction can be retried (alias).
     */
    public function canBeRetried(): bool
    {
        return $this->canRetry();
    }

    /**
     * Mark transaction as confirmed.
     */
    public function markAsConfirmed(): bool
    {
        return $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    /**
     * Mark transaction as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as processing.
     */
    public function markAsProcessing(): bool
    {
        return $this->update(['status' => self::STATUS_PROCESSING]);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(string $reason = null): bool
    {
        $metadata = $this->metadata ?? [];
        if ($reason) {
            $metadata['failure_reason'] = $reason;
            $metadata['failed_at'] = now()->toISOString();
        }

        return $this->update([
            'status' => self::STATUS_FAILED,
            'metadata' => $metadata,
            'failed_at' => now(),
            'retry_until' => now()->addHours(24), // Allow retry for 24 hours
        ]);
    }

    /**
     * Mark transaction as cancelled.
     */
    public function markAsCancelled(string $reason = null): bool
    {
        $metadata = $this->metadata ?? [];
        if ($reason) {
            $metadata['cancellation_reason'] = $reason;
            $metadata['cancelled_at'] = now()->toISOString();
        }

        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Increment retry count.
     */
    public function incrementRetryCount(): bool
    {
        return $this->increment('retry_count');
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_CREDIT:
                return number_format($this->amount, 0) . ' credits';
            case self::TYPE_NAIRA:
            case self::TYPE_EARNINGS:
                return '₦' . number_format($this->amount, 2);
            default:
                return (string) $this->amount;
        }
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed transactions.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for retryable transactions.
     */
    public function scopeRetryable($query)
    {
        return $query->where('status', self::STATUS_FAILED)
                    ->where('retry_count', '<', 3)
                    ->where('retry_until', '>', now());
    }

    /**
     * Scope by category.
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}