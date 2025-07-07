<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ad extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'url',
        'banner',
        'status',
        'earnings_per_view',
        'max_participants',
        'start_date',
        'end_date',
        'created_by_admin_id',
        'instructions',
        'terms_and_conditions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'earnings_per_view' => 'decimal:2',
        'max_participants' => 'integer',
    ];

    /**
     * Ad statuses.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Default earnings per view (₦0.3).
     */
    const DEFAULT_EARNINGS_PER_VIEW = 0.30;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ad) {
            if (is_null($ad->earnings_per_view)) {
                $ad->earnings_per_view = self::DEFAULT_EARNINGS_PER_VIEW;
            }
            if (is_null($ad->status)) {
                $ad->status = self::STATUS_DRAFT;
            }
        });
    }

    /**
     * Get the admin user who created this ad.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    /**
     * Get the ad tasks for this ad.
     */
    public function adTasks(): HasMany
    {
        return $this->hasMany(AdTask::class);
    }

    /**
     * Get active ad tasks.
     */
    public function activeAdTasks(): HasMany
    {
        return $this->hasMany(AdTask::class)->where('status', AdTask::STATUS_ACTIVE);
    }

    /**
     * Get completed ad tasks.
     */
    public function completedAdTasks(): HasMany
    {
        return $this->hasMany(AdTask::class)->where('status', AdTask::STATUS_COMPLETED);
    }

    /**
     * Check if ad is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               (!$this->start_date || $this->start_date->isPast()) &&
               (!$this->end_date || $this->end_date->isFuture());
    }

    /**
     * Check if ad is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED ||
               ($this->end_date && $this->end_date->isPast());
    }

    /**
     * Check if ad has reached max participants.
     */
    public function hasReachedMaxParticipants(): bool
    {
        if (!$this->max_participants) {
            return false;
        }
        
        return $this->adTasks()->count() >= $this->max_participants;
    }

    /**
     * Check if user can participate in this ad.
     */
    public function canUserParticipate(User $user): bool
    {
        if (!$this->isActive() || $this->hasReachedMaxParticipants()) {
            return false;
        }

        // Check if user is flagged
        if ($user->isFlaggedForAds()) {
            return false;
        }

        // Check if user already has an active ad task
        if ($user->hasActiveAdTask()) {
            return false;
        }

        // Check if user already participated in this specific ad
        if ($this->adTasks()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get current participant count.
     */
    public function getCurrentParticipantCount(): int
    {
        return $this->adTasks()->count();
    }

    /**
     * Get participants count attribute.
     */
    public function getParticipantsCountAttribute(): int
    {
        return $this->getCurrentParticipantCount();
    }

    /**
     * Get remaining slots.
     */
    public function getRemainingSlots(): ?int
    {
        if (!$this->max_participants) {
            return null;
        }
        
        return max(0, $this->max_participants - $this->getCurrentParticipantCount());
    }

    /**
     * Get total earnings paid for this ad.
     */
    public function getTotalEarningsPaid(): float
    {
        return $this->adTasks()
            ->where('status', AdTask::STATUS_APPROVED)
            ->sum('earnings_amount');
    }

    /**
     * Get average view count for approved tasks.
     */
    public function getAverageViewCount(): float
    {
        return $this->adTasks()
            ->where('status', AdTask::STATUS_APPROVED)
            ->avg('view_count') ?? 0;
    }

    /**
     * Mark ad as active.
     */
    public function markAsActive(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Mark ad as paused.
     */
    public function markAsPaused(): bool
    {
        return $this->update(['status' => self::STATUS_PAUSED]);
    }

    /**
     * Mark ad as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Mark ad as expired.
     */
    public function markAsExpired(): bool
    {
        return $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Get banner URL.
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner) {
            return null;
        }
        
        if (Str::startsWith($this->banner, ['http://', 'https://'])) {
            return $this->banner;
        }
        
        return asset('storage/' . $this->banner);
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_EXPIRED => 'Expired',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_ACTIVE => 'green',
            self::STATUS_PAUSED => 'yellow',
            self::STATUS_COMPLETED => 'blue',
            self::STATUS_EXPIRED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get copy content for users.
     */
    public function getCopyContentAttribute(): string
    {
        $content = $this->description;
        
        if ($this->url) {
            $content .= "\n\n🔗 " . $this->url;
        }
        
        $content .= "\n\n#YapaAd #ShareAndEarn";
        
        return $content;
    }

    /**
     * Scope for active ads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Scope for expired ads.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
                    ->orWhere('end_date', '<', now());
    }

    /**
     * Scope for available ads (active and not full).
     */
    public function scopeAvailable($query)
    {
        return $query->active()
                    ->where(function ($q) {
                        $q->whereNull('max_participants')
                          ->orWhereRaw('(SELECT COUNT(*) FROM ad_tasks WHERE ad_id = ads.id) < max_participants');
                    });
    }

    /**
     * Scope for ads created by specific admin.
     */
    public function scopeCreatedBy($query, $adminId)
    {
        return $query->where('created_by_admin_id', $adminId);
    }

    /**
     * Scope for ads within date range.
     */
    public function scopeWithinDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return $query;
    }
}