<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'limit',
        'location',
        'status',
        'type',
        'cost_in_credits',
        'download_vcf_path',
        'created_by_admin',
        'auto_close_at',
        'description',
        'admin_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_by_admin' => 'boolean',
        'cost_in_credits' => 'integer',
        'limit' => 'integer',
        'auto_close_at' => 'datetime',
    ];

    /**
     * Batch statuses.
     */
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_FULL = 'full';
    const STATUS_EXPIRED = 'expired';

    /**
     * Batch types.
     */
    const TYPE_TRIAL = 'trial';
    const TYPE_REGULAR = 'regular';

    /**
     * Default limits.
     */
    const TRIAL_LIMIT = 30;
    const REGULAR_LIMIT = 100;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            if (is_null($batch->auto_close_at)) {
                $batch->auto_close_at = now()->addDays(config('app.batch_auto_close_days', 7));
            }
            if (is_null($batch->status)) {
                $batch->status = self::STATUS_OPEN;
            }
            if (is_null($batch->limit)) {
                $batch->limit = $batch->type === self::TYPE_TRIAL ? self::TRIAL_LIMIT : self::REGULAR_LIMIT;
            }
        });
    }

    /**
     * Get the interests associated with this batch.
     */
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'batch_interests')
            ->withTimestamps();
    }

    /**
     * Get the batch members.
     */
    public function members(): HasMany
    {
        return $this->hasMany(BatchMember::class);
    }

    /**
     * Get the admin user who created this batch.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the batch shares.
     */
    public function batchShares(): HasMany
    {
        return $this->hasMany(BatchShare::class);
    }

    /**
     * Check if batch is open.
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if batch is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Check if batch is full.
     */
    public function isFull(): bool
    {
        return $this->status === self::STATUS_FULL || $this->members()->count() >= $this->limit;
    }

    /**
     * Check if batch is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED || 
               ($this->auto_close_at && $this->auto_close_at->isPast());
    }

    /**
     * Check if user can join this batch.
     */
    public function canUserJoin(User $user): bool
    {
        if (!$this->isOpen() || $this->isFull() || $this->isExpired()) {
            return false;
        }

        // Check if user is banned from joining batches
        if ($user->isBannedFromBatches()) {
            return false;
        }

        // Check if user already joined this batch
        if ($this->members()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check if user has sufficient credits for regular batches
        if ($this->type === self::TYPE_REGULAR && !$user->hasSufficientCredits($this->cost_in_credits)) {
            return false;
        }

        // For trial batches, check if user has never joined any batch before
        if ($this->type === self::TYPE_TRIAL) {
            if (!$user->hasNeverJoinedAnyBatch()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get current member count.
     */
    public function getCurrentMemberCount(): int
    {
        return $this->members()->count();
    }

    /**
     * Get remaining slots.
     */
    public function getRemainingSlots(): int
    {
        return max(0, $this->limit - $this->getCurrentMemberCount());
    }

    /**
     * Get fill percentage.
     */
    public function getFillPercentage(): float
    {
        return ($this->getCurrentMemberCount() / $this->limit) * 100;
    }

    /**
     * Mark batch as full.
     */
    public function markAsFull(): bool
    {
        return $this->update(['status' => self::STATUS_FULL]);
    }

    /**
     * Mark batch as closed.
     */
    public function markAsClosed(): bool
    {
        return $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Mark batch as expired.
     */
    public function markAsExpired(): bool
    {
        return $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Calculate match score for a user.
     */
    public function calculateMatchScore(User $user): float
    {
        $locationWeight = config('app.batch_location_weight', 0.6);
        $interestWeight = config('app.batch_interest_weight', 0.4);
        
        $locationScore = $this->calculateLocationScore($user);
        $interestScore = $this->calculateInterestScore($user);
        
        return ($locationScore * $locationWeight) + ($interestScore * $interestWeight);
    }

    /**
     * Calculate location match score.
     */
    protected function calculateLocationScore(User $user): float
    {
        if (!$user->location || !$this->location) {
            return 0.0;
        }

        // Exact match
        if (strtolower($user->location) === strtolower($this->location)) {
            return 1.0;
        }

        // Partial match (same state)
        $userParts = explode(',', $user->location);
        $batchParts = explode(',', $this->location);
        
        if (count($userParts) >= 2 && count($batchParts) >= 2) {
            $userState = trim(end($userParts));
            $batchState = trim(end($batchParts));
            
            if (strtolower($userState) === strtolower($batchState)) {
                return 0.5;
            }
        }

        return 0.0;
    }

    /**
     * Calculate interest match score.
     */
    protected function calculateInterestScore(User $user): float
    {
        $userInterests = $user->interests()->pluck('id')->toArray();
        $batchInterests = $this->interests()->pluck('id')->toArray();
        
        if (empty($userInterests) || empty($batchInterests)) {
            return 0.0;
        }
        
        $commonInterests = array_intersect($userInterests, $batchInterests);
        $totalInterests = array_unique(array_merge($userInterests, $batchInterests));
        
        return count($commonInterests) / count($totalInterests);
    }

    /**
     * Scope for open batches.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for trial batches.
     */
    public function scopeTrial($query)
    {
        return $query->where('type', self::TYPE_TRIAL);
    }

    /**
     * Scope for regular batches.
     */
    public function scopeRegular($query)
    {
        return $query->where('type', self::TYPE_REGULAR);
    }

    /**
     * Scope for expired batches.
     */
    public function scopeExpired($query)
    {
        return $query->where('auto_close_at', '<', now())
                    ->orWhere('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope for available batches (open and not full).
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_OPEN)
                    ->where('auto_close_at', '>', now())
                    ->whereRaw('(SELECT COUNT(*) FROM batch_members WHERE batch_id = batches.id) < batches.limit');
    }

    /**
     * Scope to match user preferences.
     */
    public function scopeMatchingUser($query, User $user)
    {
        return $query->available()
                    ->where(function ($q) use ($user) {
                        // Location matching
                        if ($user->location) {
                            $q->where('location', 'like', '%' . $user->location . '%');
                        }
                    })
                    ->whereHas('interests', function ($q) use ($user) {
                        // Interest matching
                        $userInterestIds = $user->interests()->pluck('interests.id');
                        if ($userInterestIds->isNotEmpty()) {
                            $q->whereIn('interests.id', $userInterestIds);
                        }
                    });
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_FULL => 'Full',
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
            self::STATUS_OPEN => 'green',
            self::STATUS_CLOSED => 'gray',
            self::STATUS_FULL => 'blue',
            self::STATUS_EXPIRED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get share link for this batch.
     */
    public function getShareLink(string $referralCode): string
    {
        return url('/batch/' . $this->id . '?ref=' . $referralCode);
    }
}