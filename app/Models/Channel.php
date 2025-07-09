<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'whatsapp_link',
        'description',
        'sample_screenshot',
        'status',
        'price_per_24_hours',
        'admin_notes',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'follower_count' => 'integer',
        'price_per_24_hours' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Channel statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Available niches.
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
     * Get the channel ad applications.
     */
    public function channelAdApplications(): HasMany
    {
        return $this->hasMany(ChannelAdApplication::class);
    }

    /**
     * Get the channel ad bookings.
     */
    public function channelAdBookings(): HasMany
    {
        return $this->hasMany(ChannelAdBooking::class);
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
     * Approve the channel.
     */
    public function approve(?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
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
            'approved_at' => null,
        ]);
    }

    /**
     * Get the niche display name.
     */
    public function getNicheDisplayAttribute(): string
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
     * Scope for pending channels.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope by niche.
     */
    public function scopeByNiche($query, string $niche)
    {
        return $query->where('niche', $niche);
    }

    /**
     * Scope by minimum followers.
     */
    public function scopeMinFollowers($query, int $minFollowers)
    {
        return $query->where('follower_count', '>=', $minFollowers);
    }
}