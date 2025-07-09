<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChannelAd extends Model
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
        'content',
        'media_url',
        'duration_days',
        'budget',
        'payment_per_channel',
        'max_channels',
        'target_niches',
        'min_followers',
        'status',
        'start_date',
        'end_date',
        'created_by_admin_id',
        'instructions',
        'requirements',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration_days' => 'integer',
        'budget' => 'decimal:2',
        'payment_per_channel' => 'decimal:2',
        'max_channels' => 'integer',
        'min_followers' => 'integer',
        'target_niches' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Channel ad statuses.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($channelAd) {
            if (is_null($channelAd->status)) {
                $channelAd->status = self::STATUS_DRAFT;
            }
        });
    }

    /**
     * Get the admin user who created this channel ad.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    /**
     * Get the channel ad applications.
     */
    public function channelAdApplications(): HasMany
    {
        return $this->hasMany(ChannelAdApplication::class);
    }

    /**
     * Get approved applications.
     */
    public function approvedApplications(): HasMany
    {
        return $this->hasMany(ChannelAdApplication::class)
                    ->where('status', ChannelAdApplication::STATUS_APPROVED);
    }

    /**
     * Get running applications.
     */
    public function runningApplications(): HasMany
    {
        return $this->hasMany(ChannelAdApplication::class)
                    ->where('status', ChannelAdApplication::STATUS_RUNNING);
    }

    /**
     * Get completed applications.
     */
    public function completedApplications(): HasMany
    {
        return $this->hasMany(ChannelAdApplication::class)
                    ->where('status', ChannelAdApplication::STATUS_COMPLETED);
    }

    /**
     * Check if channel ad is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               (!$this->start_date || $this->start_date->isPast()) &&
               (!$this->end_date || $this->end_date->isFuture());
    }

    /**
     * Check if channel ad is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED ||
               ($this->end_date && $this->end_date->isPast());
    }

    /**
     * Check if channel ad has reached max channels.
     */
    public function hasReachedMaxChannels(): bool
    {
        if (!$this->max_channels) {
            return false;
        }
        
        return $this->approvedApplications()->count() >= $this->max_channels;
    }

    /**
     * Check if channel can apply.
     */
    public function canChannelApply($channel): bool
    {
        if (!$this->isActive() || $this->hasReachedMaxChannels()) {
            return false;
        }

        // Check if channel is approved
        if ($channel->status !== 'approved') {
            return false;
        }

        // Check minimum followers requirement
        if ($this->min_followers && $channel->follower_count < $this->min_followers) {
            return false;
        }

        // Check niche targeting
        if (!empty($this->target_niches) && !in_array($channel->niche, $this->target_niches)) {
            return false;
        }

        // Check if channel already applied
        if ($this->channelAdApplications()->where('channel_id', $channel->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get current application count.
     */
    public function getCurrentApplicationCount(): int
    {
        return $this->channelAdApplications()->count();
    }

    /**
     * Get approved application count.
     */
    public function getApprovedApplicationCount(): int
    {
        return $this->approvedApplications()->count();
    }

    /**
     * Get remaining slots.
     */
    public function getRemainingSlots(): ?int
    {
        if (!$this->max_channels) {
            return null;
        }
        
        return max(0, $this->max_channels - $this->getApprovedApplicationCount());
    }

    /**
     * Get total budget spent.
     */
    public function getTotalBudgetSpent(): float
    {
        return $this->completedApplications()
                    ->where('escrow_status', ChannelAdApplication::ESCROW_STATUS_RELEASED)
                    ->sum('escrow_amount');
    }

    /**
     * Get remaining budget.
     */
    public function getRemainingBudget(): float
    {
        return max(0, $this->budget - $this->getTotalBudgetSpent());
    }

    /**
     * Check if budget is sufficient for new application.
     */
    public function hasSufficientBudget(): bool
    {
        return $this->getRemainingBudget() >= $this->payment_per_channel;
    }

    /**
     * Get target niches display names.
     */
    public function getTargetNichesDisplay(): array
    {
        if (empty($this->target_niches)) {
            return [];
        }

        // Define niches locally to avoid Channel model dependency
        $niches = [
            'technology' => 'Technology',
            'lifestyle' => 'Lifestyle',
            'business' => 'Business',
            'entertainment' => 'Entertainment',
            'education' => 'Education',
            'health' => 'Health & Fitness',
            'travel' => 'Travel',
            'food' => 'Food & Cooking',
            'fashion' => 'Fashion & Beauty',
            'sports' => 'Sports',
            'music' => 'Music',
            'gaming' => 'Gaming',
            'news' => 'News & Politics',
            'finance' => 'Finance',
            'automotive' => 'Automotive',
            'real_estate' => 'Real Estate',
            'parenting' => 'Parenting',
            'pets' => 'Pets & Animals',
            'diy' => 'DIY & Crafts',
            'science' => 'Science',
            'art' => 'Art & Design',
            'photography' => 'Photography',
            'comedy' => 'Comedy',
            'spirituality' => 'Spirituality',
            'other' => 'Other'
        ];

        return array_map(function ($niche) use ($niches) {
            return $niches[$niche] ?? $niche;
        }, $this->target_niches);
    }

    /**
     * Scope for active channel ads.
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
     * Scope for channel ads by niche.
     */
    public function scopeByNiche($query, string $niche)
    {
        return $query->whereJsonContains('target_niches', $niche);
    }

    /**
     * Scope for channel ads with minimum followers.
     */
    public function scopeWithMinFollowers($query, int $followers)
    {
        return $query->where(function ($q) use ($followers) {
            $q->whereNull('min_followers')
              ->orWhere('min_followers', '<=', $followers);
        });
    }
}