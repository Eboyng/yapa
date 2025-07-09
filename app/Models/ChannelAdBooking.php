<?php

namespace App\Models;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChannelAdBooking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'channel_id',
        'title',
        'description',
        'url',
        'images',
        'duration_hours',
        'total_amount',
        'status',
        'payment_method',
        'payment_reference',
        'escrow_transaction_id',
        'escrow_status',
        'booked_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'started_at',
        'completed_at',
        'proof_screenshot',
        'proof_submitted_at',
        'proof_approved_at',
        'admin_notes',
        'auto_cancelled_at',
        'refunded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array',
        'duration_hours' => 'integer',
        'total_amount' => 'decimal:2',
        'booked_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'proof_submitted_at' => 'datetime',
        'proof_approved_at' => 'datetime',
        'auto_cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Booking statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RUNNING = 'running';
    const STATUS_PROOF_SUBMITTED = 'proof_submitted';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DISPUTED = 'disputed';

    /**
     * Payment methods.
     */
    const PAYMENT_WALLET = 'wallet';
    const PAYMENT_PAYSTACK = 'paystack';

    /**
     * Escrow statuses.
     */
    const ESCROW_STATUS_PENDING = 'pending';
    const ESCROW_STATUS_HELD = 'held';
    const ESCROW_STATUS_RELEASED = 'released';
    const ESCROW_STATUS_REFUNDED = 'refunded';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (is_null($booking->status)) {
                $booking->status = self::STATUS_PENDING;
            }
            if (is_null($booking->booked_at)) {
                $booking->booked_at = now();
            }
            if (is_null($booking->escrow_status)) {
                $booking->escrow_status = self::ESCROW_STATUS_PENDING;
            }
        });
    }

    /**
     * Get the user who booked the ad.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the channel where the ad is booked.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the escrow transaction.
     */
    public function escrowTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'escrow_transaction_id');
    }

    /**
     * Get related transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'related_id')
                    ->where('category', 'channel_ad_booking');
    }

    /**
     * Check if booking is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if booking is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if booking is running.
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Check if booking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if booking is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if escrow is held.
     */
    public function isEscrowHeld(): bool
    {
        return $this->escrow_status === self::ESCROW_STATUS_HELD;
    }

    /**
     * Check if booking has expired (48 hours without acceptance).
     */
    public function hasExpired(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        return $this->booked_at->addHours(48)->isPast();
    }

    /**
     * Accept the booking.
     */
    public function accept(): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending bookings can be accepted');
        }

        if ($this->hasExpired()) {
            throw new \InvalidArgumentException('Booking has expired and cannot be accepted');
        }

        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reject the booking and refund.
     */
    public function reject(string $reason): void
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED])) {
            throw new \InvalidArgumentException('Only pending or accepted bookings can be rejected');
        }

        DB::transaction(function () use ($reason) {
            // Refund escrow if it was held
            if ($this->escrow_status === self::ESCROW_STATUS_HELD && $this->escrow_transaction_id) {
                $transactionService = app(TransactionService::class);
                $transactionService->refundEscrow(
                    $this->escrow_transaction_id,
                    "Booking rejected - Refund for channel ad booking: {$this->title}"
                );
                
                $escrowStatus = self::ESCROW_STATUS_REFUNDED;
            } else {
                $escrowStatus = $this->escrow_status;
            }
            
            $this->update([
                'status' => self::STATUS_REJECTED,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'escrow_status' => $escrowStatus,
                'refunded_at' => now(),
            ]);
        });
    }

    /**
     * Start running the ad.
     */
    public function startRunning(): void
    {
        if ($this->status !== self::STATUS_ACCEPTED) {
            throw new \InvalidArgumentException('Only accepted bookings can be started');
        }

        $this->update([
            'status' => self::STATUS_RUNNING,
            'started_at' => now(),
        ]);
    }

    /**
     * Submit proof of completion.
     */
    public function submitProof(string $screenshot): void
    {
        if ($this->status !== self::STATUS_RUNNING) {
            throw new \InvalidArgumentException('Only running bookings can submit proof');
        }

        $this->update([
            'status' => self::STATUS_PROOF_SUBMITTED,
            'proof_screenshot' => $screenshot,
            'proof_submitted_at' => now(),
        ]);
    }

    /**
     * Approve proof and release escrow.
     */
    public function approveProofAndReleaseEscrow(): void
    {
        if ($this->status !== self::STATUS_PROOF_SUBMITTED) {
            throw new \InvalidArgumentException('Only bookings with submitted proof can be approved');
        }

        DB::transaction(function () {
            if (!$this->escrow_transaction_id) {
                throw new \InvalidArgumentException('No escrow transaction found');
            }
            
            $transactionService = app(TransactionService::class);
            $result = $transactionService->releaseEscrow(
                $this->escrow_transaction_id,
                $this->channel->user,
                "Payment for channel ad booking: {$this->title}"
            );
            
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'completed_at' => now(),
                'proof_approved_at' => now(),
                'escrow_status' => self::ESCROW_STATUS_RELEASED,
            ]);
        });
    }

    /**
     * Auto-cancel expired bookings.
     */
    public function autoCancelIfExpired(): bool
    {
        if (!$this->hasExpired()) {
            return false;
        }

        DB::transaction(function () {
            // Refund escrow if it was held
            if ($this->escrow_status === self::ESCROW_STATUS_HELD && $this->escrow_transaction_id) {
                $transactionService = app(TransactionService::class);
                $transactionService->refundEscrow(
                    $this->escrow_transaction_id,
                    "Auto-cancelled - Booking expired after 48 hours: {$this->title}"
                );
            }
            
            $this->update([
                'status' => self::STATUS_CANCELLED,
                'auto_cancelled_at' => now(),
                'escrow_status' => self::ESCROW_STATUS_REFUNDED,
                'refunded_at' => now(),
            ]);
        });

        return true;
    }

    /**
     * Calculate total amount based on duration and channel price.
     */
    public static function calculateAmount(Channel $channel, int $durationHours): float
    {
        if (!$channel->price_per_24_hours) {
            throw new \InvalidArgumentException('Channel does not have pricing set');
        }

        $days = $durationHours / 24;
        return $channel->price_per_24_hours * $days;
    }

    /**
     * Scope for pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for expired bookings.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('booked_at', '<=', now()->subHours(48));
    }

    /**
     * Scope for channel owner.
     */
    public function scopeForChannelOwner($query, int $userId)
    {
        return $query->whereHas('channel', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}