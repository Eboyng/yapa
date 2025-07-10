<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionService;

class ChannelPurchase extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'buyer_id',
        'channel_sale_id',
        'price',
        'escrow_transaction_id',
        'status',
        'admin_note',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'price' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Purchase statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_ESCROW = 'in_escrow';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the buyer.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the channel sale.
     */
    public function channelSale(): BelongsTo
    {
        return $this->belongsTo(ChannelSale::class);
    }

    /**
     * Get the seller through channel sale.
     */
    public function seller()
    {
        return $this->channelSale->user;
    }

    /**
     * Get the escrow transaction.
     */
    public function escrowTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'escrow_transaction_id');
    }

    /**
     * Check if purchase is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if purchase is in escrow.
     */
    public function isInEscrow(): bool
    {
        return $this->status === self::STATUS_IN_ESCROW;
    }

    /**
     * Check if purchase is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if purchase failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if purchase was refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Create escrow transaction for this purchase.
     */
    public function createEscrowTransaction(): Transaction
    {
        return DB::transaction(function () {
            $transactionService = app(TransactionService::class);
            
            // Create escrow transaction using existing service
            $escrowTransaction = $transactionService->debit(
                $this->buyer_id,
                $this->price,
                Transaction::TYPE_NAIRA,
                Transaction::CATEGORY_CHANNEL_SALE_ESCROW,
                "Escrow for WhatsApp Channel: {$this->channelSale->channel_name}",
                $this->id,
                'wallet',
                [
                    'channel_purchase_id' => $this->id,
                    'channel_sale_id' => $this->channel_sale_id,
                    'escrow_status' => 'held',
                ]
            );
            
            // Update purchase with escrow transaction
            $this->update([
                'escrow_transaction_id' => $escrowTransaction->id,
                'status' => self::STATUS_IN_ESCROW,
            ]);
            
            return $escrowTransaction;
        });
    }

    /**
     * Complete the purchase and release escrow to seller.
     */
    public function completeAndReleaseEscrow(): void
    {
        DB::transaction(function () {
            if (!$this->escrow_transaction_id) {
                throw new \InvalidArgumentException('No escrow transaction found');
            }
            
            if ($this->status !== self::STATUS_IN_ESCROW) {
                throw new \InvalidArgumentException('Purchase is not in escrow status');
            }
            
            $transactionService = app(TransactionService::class);
            
            // Release escrow to seller
            $result = $transactionService->releaseEscrow(
                $this->escrow_transaction_id,
                $this->seller(),
                "Payment for WhatsApp Channel: {$this->channelSale->channel_name}"
            );
            
            // Update purchase status
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
            
            // Mark channel sale as sold
            $this->channelSale->markAsSold();
        });
    }

    /**
     * Refund the purchase.
     */
    public function refund(string $reason): void
    {
        DB::transaction(function () use ($reason) {
            if (!$this->escrow_transaction_id) {
                throw new \InvalidArgumentException('No escrow transaction found');
            }
            
            if ($this->status !== self::STATUS_IN_ESCROW) {
                throw new \InvalidArgumentException('Purchase is not in escrow status');
            }
            
            $transactionService = app(TransactionService::class);
            
            // Refund escrow to buyer
            $transactionService->refundEscrow(
                $this->escrow_transaction_id,
                $reason
            );
            
            // Update purchase status
            $this->update([
                'status' => self::STATUS_REFUNDED,
                'admin_note' => $reason,
            ]);
        });
    }

    /**
     * Mark purchase as failed.
     */
    public function markAsFailed(string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'admin_note' => $reason,
        ]);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 2);
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_IN_ESCROW => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_REFUNDED => 'orange',
            default => 'gray',
        };
    }

    /**
     * Scope for pending purchases.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for in escrow purchases.
     */
    public function scopeInEscrow($query)
    {
        return $query->where('status', self::STATUS_IN_ESCROW);
    }

    /**
     * Scope for completed purchases.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for failed purchases.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for refunded purchases.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }
}