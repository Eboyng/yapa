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
        'share_count',
        'rewarded',
        'rewarded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'share_count' => 'integer',
        'rewarded' => 'boolean',
        'rewarded_at' => 'datetime',
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
        return !$this->rewarded && $this->share_count >= $threshold;
    }

    /**
     * Mark reward as claimed.
     */
    public function markRewardClaimed(): bool
    {
        return $this->update([
            'rewarded' => true,
            'rewarded_at' => now(),
        ]);
    }

    /**
     * Increment new members count.
     */
    public function incrementShareCount(): bool
    {
        return $this->increment('share_count');
    }

    /**
     * Scope for unclaimed rewards.
     */
    public function scopeUnclaimedRewards($query)
    {
        return $query->where('rewarded', false);
    }

    /**
     * Scope for eligible rewards.
     */
    public function scopeEligibleForReward($query)
    {
        $threshold = app(\App\Services\SettingService::class)->get('batch_share_threshold', 10);
        return $query->where('rewarded', false)
                    ->where('share_count', '>=', $threshold);
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