<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchShare extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'batch_id',
        'platform',
        'new_members_count',
        'reward_claimed',
        'reward_claimed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'new_members_count' => 'integer',
        'reward_claimed' => 'boolean',
        'reward_claimed_at' => 'datetime',
    ];

    /**
     * Platform constants.
     */
    const PLATFORM_WHATSAPP = 'whatsapp';
    const PLATFORM_FACEBOOK = 'facebook';
    const PLATFORM_TWITTER = 'twitter';
    const PLATFORM_COPY_LINK = 'copy_link';

    /**
     * Get the user who shared the batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the batch that was shared.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Check if reward can be claimed.
     */
    public function canClaimReward(): bool
    {
        $threshold = app(\App\Services\SettingService::class)->get('batch_share_threshold', 10);
        return !$this->reward_claimed && $this->new_members_count >= $threshold;
    }

    /**
     * Mark reward as claimed.
     */
    public function markRewardClaimed(): bool
    {
        return $this->update([
            'reward_claimed' => true,
            'reward_claimed_at' => now(),
        ]);
    }

    /**
     * Increment new members count.
     */
    public function incrementNewMembersCount(): bool
    {
        return $this->increment('new_members_count');
    }

    /**
     * Scope for unclaimed rewards.
     */
    public function scopeUnclaimedRewards($query)
    {
        return $query->where('reward_claimed', false);
    }

    /**
     * Scope for eligible rewards.
     */
    public function scopeEligibleForReward($query)
    {
        $threshold = app(\App\Services\SettingService::class)->get('batch_share_threshold', 10);
        return $query->where('reward_claimed', false)
                    ->where('new_members_count', '>=', $threshold);
    }

    /**
     * Scope by platform.
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Get available platforms.
     */
    public static function getAvailablePlatforms(): array
    {
        return [
            self::PLATFORM_WHATSAPP => 'WhatsApp',
            self::PLATFORM_FACEBOOK => 'Facebook',
            self::PLATFORM_TWITTER => 'Twitter',
            self::PLATFORM_COPY_LINK => 'Copy Link',
        ];
    }
}