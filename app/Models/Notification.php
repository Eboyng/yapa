<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Notification extends Model
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
        'title',
        'message',
        'data',
        'read_at',
        'action_url',
        'icon',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'priority' => 'integer',
    ];

    /**
     * Notification types.
     */
    const TYPE_BATCH_FULL = 'batch_full';
    const TYPE_AD_APPROVAL = 'ad_approval';
    const TYPE_AD_REJECTION = 'ad_rejection';
    const TYPE_TRANSACTION_SUCCESS = 'transaction_success';
    const TYPE_TRANSACTION_FAILURE = 'transaction_failure';
    const TYPE_CHANNEL_APPROVAL = 'channel_approval';
    const TYPE_CHANNEL_REJECTION = 'channel_rejection';
    const TYPE_ESCROW_RELEASE = 'escrow_release';
    const TYPE_DISPUTE_UPDATE = 'dispute_update';
    const TYPE_SYSTEM_ANNOUNCEMENT = 'system_announcement';
    const TYPE_GENERAL = 'general';

    /**
     * Priority levels.
     */
    const PRIORITY_LOW = 1;
    const PRIORITY_NORMAL = 2;
    const PRIORITY_HIGH = 3;
    const PRIORITY_URGENT = 4;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notification) {
            if (is_null($notification->priority)) {
                $notification->priority = self::PRIORITY_NORMAL;
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
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Get icon for notification type.
     */
    public function getTypeIcon(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match ($this->type) {
            self::TYPE_BATCH_FULL => 'heroicon-o-user-group',
            self::TYPE_AD_APPROVAL => 'heroicon-o-check-circle',
            self::TYPE_AD_REJECTION => 'heroicon-o-x-circle',
            self::TYPE_TRANSACTION_SUCCESS => 'heroicon-o-currency-dollar',
            self::TYPE_TRANSACTION_FAILURE => 'heroicon-o-exclamation-triangle',
            self::TYPE_CHANNEL_APPROVAL => 'heroicon-o-megaphone',
            self::TYPE_CHANNEL_REJECTION => 'heroicon-o-no-symbol',
            self::TYPE_ESCROW_RELEASE => 'heroicon-o-banknotes',
            self::TYPE_DISPUTE_UPDATE => 'heroicon-o-scale',
            self::TYPE_SYSTEM_ANNOUNCEMENT => 'heroicon-o-speaker-wave',
            default => 'heroicon-o-bell',
        };
    }

    /**
     * Get color for notification type.
     */
    public function getTypeColor(): string
    {
        return match ($this->type) {
            self::TYPE_AD_APPROVAL, self::TYPE_CHANNEL_APPROVAL, self::TYPE_TRANSACTION_SUCCESS, self::TYPE_ESCROW_RELEASE => 'success',
            self::TYPE_AD_REJECTION, self::TYPE_CHANNEL_REJECTION, self::TYPE_TRANSACTION_FAILURE => 'danger',
            self::TYPE_DISPUTE_UPDATE => 'warning',
            self::TYPE_SYSTEM_ANNOUNCEMENT => 'info',
            default => 'gray',
        };
    }

    /**
     * Get priority label.
     */
    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            default => 'Normal',
        };
    }

    /**
     * Create a notification.
     */
    public static function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $actionUrl = null,
        ?string $icon = null,
        int $priority = self::PRIORITY_NORMAL
    ): self {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'icon' => $icon,
            'priority' => $priority,
        ]);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by priority.
     */
    public function scopeByPriority($query, int $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for high priority notifications.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', self::PRIORITY_HIGH);
    }

    /**
     * Scope for recent notifications.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope ordered by priority and creation date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'desc');
    }
}