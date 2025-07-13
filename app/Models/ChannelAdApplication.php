<?php

namespace App\Models;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChannelAdApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'channel_id',
        'channel_ad_id',
        'status',
        'applied_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'admin_notes',
        'proof_screenshot',
        'proof_submitted_at',
        'proof_approved_at',
        'escrow_amount',
        'escrow_status',
        'escrow_released_at',
        'dispute_reason',
        'dispute_status',
        'dispute_resolved_at',
        'dispute_resolution',
        'escrow_transaction_id',
        // Marketplace fields
        'advertiser_id',
        'booking_status',
        'start_date',
        'end_date',
        'payment_status',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'proof_submitted_at' => 'datetime',
        'proof_approved_at' => 'datetime',
        'escrow_released_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'escrow_amount' => 'decimal:2',
    ];

    /**
     * Application statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RUNNING = 'running';
    const STATUS_PROOF_SUBMITTED = 'proof_submitted';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DISPUTED = 'disputed';

    /**
     * Escrow statuses.
     */
    const ESCROW_STATUS_PENDING = 'pending';
    const ESCROW_STATUS_HELD = 'held';
    const ESCROW_STATUS_RELEASED = 'released';
    const ESCROW_STATUS_REFUNDED = 'refunded';

    /**
     * Dispute statuses.
     */
    const DISPUTE_STATUS_NONE = 'none';
    const DISPUTE_STATUS_PENDING = 'pending';
    const DISPUTE_STATUS_RESOLVED = 'resolved';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (is_null($application->status)) {
                $application->status = self::STATUS_PENDING;
            }
            if (is_null($application->applied_at)) {
                $application->applied_at = now();
            }
            if (is_null($application->escrow_status)) {
                $application->escrow_status = self::ESCROW_STATUS_PENDING;
            }
            if (is_null($application->dispute_status)) {
                $application->dispute_status = self::DISPUTE_STATUS_NONE;
            }
        });
    }

    /**
     * Get the channel that applied.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the channel ad.
     */
    public function channelAd(): BelongsTo
    {
        return $this->belongsTo(ChannelAd::class);
    }

    /**
     * Get the advertiser (user who applied for the ad).
     */
    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    /**
     * Get related transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'related_id')
                    ->where('category', 'channel_ad_escrow');
    }

    /**
     * Get the escrow transaction.
     */
    public function escrowTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'escrow_transaction_id');
    }

    /**
     * Check if application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if application is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if application is running.
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Check if application is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if application is disputed.
     */
    public function isDisputed(): bool
    {
        return $this->status === self::STATUS_DISPUTED;
    }

    /**
     * Check if escrow is held.
     */
    public function isEscrowHeld(): bool
    {
        return $this->escrow_status === self::ESCROW_STATUS_HELD;
    }

    /**
     * Check if escrow is released.
     */
    public function isEscrowReleased(): bool
    {
        return $this->escrow_status === self::ESCROW_STATUS_RELEASED;
    }

    /**
     * Check if proof is submitted.
     */
    public function isProofSubmitted(): bool
    {
        return !is_null($this->proof_screenshot) && !is_null($this->proof_submitted_at);
    }

    /**
     * Check if proof is approved.
     */
    public function isProofApproved(): bool
    {
        return !is_null($this->proof_approved_at);
    }

    /**
     * Approve the application.
     */
    public function approve(?string $notes = null): void
    {
        // Escrow should already be created when application was submitted
        if ($this->escrow_status !== self::ESCROW_STATUS_HELD) {
            throw new \InvalidArgumentException('Escrow must be held before approving application');
        }
        
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'admin_notes' => $notes,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject the application and refund escrow.
     */
    public function reject(string $reason, ?string $notes = null): void
    {
        DB::transaction(function () use ($reason, $notes) {
            // Refund escrow if it was held
            if ($this->escrow_status === self::ESCROW_STATUS_HELD && $this->escrow_transaction_id) {
                $transactionService = app(TransactionService::class);
                $transactionService->refundEscrow(
                    $this->escrow_transaction_id,
                    "Application rejected - Refund for channel ad: {$this->channelAd->title}"
                );
                
                $escrowStatus = self::ESCROW_STATUS_REFUNDED;
            } else {
                $escrowStatus = $this->escrow_status;
            }
            
            $this->update([
                'status' => self::STATUS_REJECTED,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'admin_notes' => $notes,
                'approved_at' => null,
                'escrow_status' => $escrowStatus,
            ]);
        });
    }

    /**
     * Start running the ad.
     */
    public function startRunning(): void
    {
        $this->update([
            'status' => self::STATUS_RUNNING,
        ]);
    }

    /**
     * Submit proof of ad completion.
     */
    public function submitProof(string $screenshot): void
    {
        $this->update([
            'proof_screenshot' => $screenshot,
            'proof_submitted_at' => now(),
        ]);
    }

    /**
     * Approve proof and release escrow.
     */
    public function approveProofAndReleaseEscrow(): void
    {
        DB::transaction(function () {
            if (!$this->escrow_transaction_id) {
                throw new \InvalidArgumentException('No escrow transaction found');
            }
            
            $transactionService = app(TransactionService::class);
            $result = $transactionService->releaseEscrow(
                $this->escrow_transaction_id,
                $this->channel->user,
                "Payment for channel ad: {$this->channelAd->title}"
            );
            
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'proof_approved_at' => now(),
                'escrow_status' => self::ESCROW_STATUS_RELEASED,
                'escrow_released_at' => now(),
            ]);
        });
    }

    /**
     * Start dispute.
     */
    public function startDispute(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_DISPUTED,
            'dispute_reason' => $reason,
            'dispute_status' => self::DISPUTE_STATUS_PENDING,
        ]);
    }

    /**
     * Complete the application.
     */
    public function complete(): void
    {
        DB::transaction(function () {
            if (!$this->escrow_transaction_id) {
                throw new \InvalidArgumentException('No escrow transaction found');
            }
            
            $transactionService = app(TransactionService::class);
            $result = $transactionService->releaseEscrow(
                $this->escrow_transaction_id,
                $this->channel->user,
                "Payment for channel ad: {$this->channelAd->title}"
            );
            
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'completed_at' => now(),
                'escrow_status' => self::ESCROW_STATUS_RELEASED,
                'escrow_released_at' => now(),
            ]);
        });
    }

    /**
     * Mark application as disputed.
     */
    public function dispute(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_DISPUTED,
            'dispute_reason' => $reason,
            'dispute_status' => self::DISPUTE_STATUS_PENDING,
            'disputed_at' => now(),
        ]);
    }

    /**
     * Resolve dispute.
     */
    public function resolveDispute(string $resolution, bool $releaseEscrow = true): void
    {
        DB::transaction(function () use ($resolution, $releaseEscrow) {
            if (!$this->escrow_transaction_id) {
                throw new \InvalidArgumentException('No escrow transaction found');
            }
            
            $transactionService = app(TransactionService::class);
            
            if ($releaseEscrow) {
                // Release escrow to channel owner
                $result = $transactionService->releaseEscrow(
                    $this->escrow_transaction_id,
                    $this->channel->user,
                    "Dispute resolved - Payment for channel ad: {$this->channelAd->title}"
                );
            } else {
                // Refund escrow to advertiser
                $transactionService->refundEscrow(
                    $this->escrow_transaction_id,
                    "Dispute resolved - Refund for channel ad: {$this->channelAd->title}"
                );
            }
            
            $this->update([
                'dispute_status' => self::DISPUTE_STATUS_RESOLVED,
                'dispute_resolved_at' => now(),
                'dispute_resolution' => $resolution,
                'status' => $releaseEscrow ? self::STATUS_COMPLETED : self::STATUS_REJECTED,
                'escrow_status' => $releaseEscrow ? self::ESCROW_STATUS_RELEASED : self::ESCROW_STATUS_REFUNDED,
                'escrow_released_at' => $releaseEscrow ? now() : null,
            ]);
        });
    }
}