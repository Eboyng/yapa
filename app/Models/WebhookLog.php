<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'source',
        'event_type',
        'reference',
        'payload',
        'headers',
        'status',
        'processing_result',
        'signature',
        'verified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'verified' => 'boolean',
    ];

    /**
     * Webhook statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FAILED = 'failed';

    /**
     * Webhook sources.
     */
    const SOURCE_PAYSTACK = 'paystack';
    const SOURCE_KUDISMS = 'kudisms';

    /**
     * Create a webhook log entry.
     */
    public static function log(
        string $source,
        string $eventType,
        array $payload,
        ?string $reference = null,
        ?array $headers = null,
        ?string $signature = null
    ): self {
        return static::create([
            'source' => $source,
            'event_type' => $eventType,
            'reference' => $reference,
            'payload' => $payload,
            'headers' => $headers,
            'signature' => $signature,
            'status' => self::STATUS_PENDING,
            'verified' => false,
        ]);
    }

    /**
     * Mark as processed.
     */
    public function markAsProcessed(string $result = null): bool
    {
        return $this->update([
            'status' => self::STATUS_PROCESSED,
            'processing_result' => $result,
        ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $error): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'processing_result' => $error,
        ]);
    }

    /**
     * Mark as verified.
     */
    public function markAsVerified(): bool
    {
        return $this->update(['verified' => true]);
    }

    /**
     * Check if webhook is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if webhook is processed.
     */
    public function isProcessed(): bool
    {
        return $this->status === self::STATUS_PROCESSED;
    }

    /**
     * Check if webhook is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Scope by source.
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope by event type.
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for verified webhooks.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    /**
     * Scope for unverified webhooks.
     */
    public function scopeUnverified($query)
    {
        return $query->where('verified', false);
    }
}