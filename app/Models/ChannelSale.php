<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChannelSale extends Model
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
        'user_id',
        'channel_name',
        'whatsapp_number',
        'category',
        'audience_size',
        'engagement_rate',
        'description',
        'price',
        'screenshots',
        'status',
        'visibility',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'audience_size' => 'integer',
        'engagement_rate' => 'decimal:2',
        'price' => 'decimal:2',
        'screenshots' => 'array',
        'visibility' => 'boolean',
    ];

    /**
     * Channel sale statuses.
     */
    const STATUS_LISTED = 'listed';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_SOLD = 'sold';
    const STATUS_REMOVED = 'removed';

    /**
     * Channel categories.
     */
    const CATEGORIES = [
        'gossip' => 'Gossip',
        'crypto' => 'Cryptocurrency',
        'education' => 'Education',
        'entertainment' => 'Entertainment',
        'business' => 'Business',
        'technology' => 'Technology',
        'health' => 'Health & Fitness',
        'lifestyle' => 'Lifestyle',
        'news' => 'News',
        'sports' => 'Sports',
        'other' => 'Other',
    ];

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
     * Get the user that owns the channel sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchases for this channel sale.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(ChannelPurchase::class);
    }

    /**
     * Get the successful purchase for this channel sale.
     */
    public function successfulPurchase()
    {
        return $this->purchases()->where('status', ChannelPurchase::STATUS_COMPLETED)->first();
    }

    /**
     * Check if the channel is available for purchase.
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_LISTED && $this->visibility;
    }

    /**
     * Check if the channel is sold.
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    /**
     * Check if the channel is under review.
     */
    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_UNDER_REVIEW;
    }

    /**
     * Mark the channel as sold.
     */
    public function markAsSold(): bool
    {
        return $this->update(['status' => self::STATUS_SOLD]);
    }

    /**
     * Mark the channel as listed.
     */
    public function markAsListed(): bool
    {
        return $this->update(['status' => self::STATUS_LISTED]);
    }

    /**
     * Mark the channel as removed.
     */
    public function markAsRemoved(): bool
    {
        return $this->update(['status' => self::STATUS_REMOVED]);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 2);
    }

    /**
     * Get formatted audience size.
     */
    public function getFormattedAudienceSizeAttribute(): string
    {
        if ($this->audience_size >= 1000000) {
            return number_format($this->audience_size / 1000000, 1) . 'M';
        } elseif ($this->audience_size >= 1000) {
            return number_format($this->audience_size / 1000, 1) . 'K';
        }
        return number_format($this->audience_size);
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_LISTED => 'green',
            self::STATUS_UNDER_REVIEW => 'yellow',
            self::STATUS_SOLD => 'blue',
            self::STATUS_REMOVED => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope for available channels.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_LISTED)
                    ->where('visibility', true);
    }

    /**
     * Scope for listed channels.
     */
    public function scopeListed($query)
    {
        return $query->where('status', self::STATUS_LISTED);
    }

    /**
     * Scope for sold channels.
     */
    public function scopeSold($query)
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    /**
     * Scope for under review channels.
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', self::STATUS_UNDER_REVIEW);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by price range.
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope by audience size range.
     */
    public function scopeByAudienceSize($query, $minSize = null, $maxSize = null)
    {
        if ($minSize !== null) {
            $query->where('audience_size', '>=', $minSize);
        }
        if ($maxSize !== null) {
            $query->where('audience_size', '<=', $maxSize);
        }
        return $query;
    }
}