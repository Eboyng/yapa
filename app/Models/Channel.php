<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'niche',
        'follower_count',
        'sample_screenshot',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'is_featured',
        'featured_priority',
        'whatsapp_link',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'follower_count' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_featured' => 'boolean',
        'featured_priority' => 'integer',
    ];

    /**
     * Channel statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Channel niches.
     */
    const NICHES = [
        'technology' => 'Technology',
        'business' => 'Business',
        'entertainment' => 'Entertainment',
        'sports' => 'Sports',
        'news' => 'News',
        'education' => 'Education',
        'lifestyle' => 'Lifestyle',
        'health' => 'Health',
        'finance' => 'Finance',
        'travel' => 'Travel',
        'food' => 'Food',
        'fashion' => 'Fashion',
        'music' => 'Music',
        'gaming' => 'Gaming',
        'other' => 'Other',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($channel) {
            if (is_null($channel->status)) {
                $channel->status = self::STATUS_PENDING;
            }
        });
    }

    /**
     * Get the user that owns the channel.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the channel.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the channel ad applications.
     */
    public function channelAdApplications(): HasMany
    {
        return $this->hasMany(ChannelAdApplication::class);
    }

    /**
     * Check if channel is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if channel is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if channel is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if channel is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Check if channel is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Approve the channel.
     */
    public function approve(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $notes,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject the channel.
     */
    public function reject(string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Suspend the channel.
     */
    public function suspend(?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Get niche display name.
     */
    public function getNicheDisplayName(): string
    {
        return self::NICHES[$this->niche] ?? $this->niche;
    }

    /**
     * Scope for approved channels.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for featured channels.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->orderBy('featured_priority', 'desc')
                    ->orderBy('follower_count', 'desc');
    }

    /**
     * Scope for channels by niche.
     */
    public function scopeByNiche($query, string $niche)
    {
        return $query->where('niche', $niche);
    }
}