<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NotificationLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'channel',
        'recipient',
        'subject',
        'message',
        'status',
        'sent_at',
        'delivered_at',
        'failed_at',
        'retry_count',
        'max_retries',
        'error_message',
        'gateway_response',
        'gateway_message_id',
        'metadata',
        'related_model_type',
        'related_model_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Notification types.
     */
    const TYPE_OTP = 'otp';
    const TYPE_BATCH_FULL = 'batch_full';
    const TYPE_AD_APPROVAL = 'ad_approval';
    const TYPE_AD_REJECTION = 'ad_rejection';
    const TYPE_TRANSACTION_SUCCESS = 'transaction_success';
    const TYPE_TRANSACTION_FAILURE = 'transaction_failure';
    const TYPE_CHANNEL_APPROVAL = 'channel_approval';
    const TYPE_CHANNEL_REJECTION = 'channel_rejection';
    const TYPE_ESCROW_RELEASE = 'escrow_release';
    const TYPE_DISPUTE_NOTIFICATION = 'dispute_notification';
    const TYPE_WEEKLY_REPORT = 'weekly_report';
    const TYPE_GENERAL = 'general';

    /**
     * Notification channels.
     */
    const CHANNEL_WHATSAPP = 'whatsapp';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_EMAIL = 'email';

    /**
     * Notification statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notification) {
            if (is_null($notification->status)) {
                $notification->status = self::STATUS_PENDING;
            }
            if (is_null($notification->retry_count)) {
                $notification->retry_count = 0;
            }
            if (is_null($notification->max_retries)) {
                $notification->max_retries = 3;
            }
        });
    }

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model.
     */
    public function relatedModel()
    {
        if ($this->related_model_type && $this->related_model_id) {
            return $this->morphTo('related', 'related_model_type', 'related_model_id');
        }
        return null;
    }

    /**
     * Check if notification is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if notification is sent.
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Check if notification is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Check if notification is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if notification can be retried.
     */
    public function canRetry(): bool
    {
        return $this->isFailed() && $this->retry_count < $this->max_retries;
    }

    /**
     * Mark as sent.
     */
    public function markAsSent(?string $gatewayMessageId = null, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'gateway_message_id' => $gatewayMessageId,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Mark as delivered.
     */
    public function markAsDelivered(?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse ?? []),
        ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $errorMessage, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Increment retry count.
     */
    public function incrementRetry(): void
    {
        $this->increment('retry_count');
        $this->update(['status' => self::STATUS_PENDING]);
    }

    /**
     * Mark as cancelled.
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Create a notification log entry.
     */
    public static function createLog(
        ?int $userId,
        string $type,
        string $channel,
        string $recipient,
        string $message,
        ?string $subject = null,
        ?array $metadata = null,
        ?string $relatedModelType = null,
        ?int $relatedModelId = null,
        int $maxRetries = 3
    ): self {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message,
            'metadata' => $metadata,
            'related_model_type' => $relatedModelType,
            'related_model_id' => $relatedModelId,
            'max_retries' => $maxRetries,
        ]);
    }

    /**
     * Scope by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by channel.
     */
    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for failed notifications that can be retried.
     */
    public function scopeRetryable($query)
    {
        return $query->where('status', self::STATUS_FAILED)
                    ->whereColumn('retry_count', '<', 'max_retries');
    }

    /**
     * Scope for notifications sent today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for notifications sent this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}