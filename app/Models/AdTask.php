<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdTask extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ad_id',
        'user_id',
        'status',
        'started_at',
        'screenshot_uploaded_at',
        'screenshot_path',
        'view_count',
        'earnings_amount',
        'reviewed_at',
        'reviewed_by_admin_id',
        'rejection_reason',
        'appeal_message',
        'appeal_submitted_at',
        'appeal_reviewed_at',
        'appeal_reviewed_by_admin_id',
        'appeal_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'screenshot_uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'appeal_submitted_at' => 'datetime',
        'appeal_reviewed_at' => 'datetime',
        'view_count' => 'integer',
        'earnings_amount' => 'decimal:2',
    ];

    /**
     * Task statuses.
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_SCREENSHOT_UPLOADED = 'screenshot_uploaded';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Appeal statuses.
     */
    const APPEAL_STATUS_PENDING = 'pending';
    const APPEAL_STATUS_APPROVED = 'approved';
    const APPEAL_STATUS_REJECTED = 'rejected';

    /**
     * Task expiry hours (24 hours to upload screenshot).
     */
    const TASK_EXPIRY_HOURS = 24;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($adTask) {
            if (is_null($adTask->started_at)) {
                $adTask->started_at = now();
            }
            if (is_null($adTask->status)) {
                $adTask->status = self::STATUS_ACTIVE;
            }
        });

        static::updated(function ($adTask) {
            // Auto-calculate earnings when approved
            if ($adTask->status === self::STATUS_APPROVED && !$adTask->earnings_amount) {
                $earnings = $adTask->calculateEarnings();
                $adTask->update(['earnings_amount' => $earnings]);
                
                // Add earnings to user's balance
                $adTask->addEarningsToUser();
            }
        });
    }

    /**
     * Get the ad this task belongs to.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the user this task belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed this task.
     */
    public function reviewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }

    /**
     * Get the admin who reviewed the appeal.
     */
    public function appealReviewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appeal_reviewed_by_admin_id');
    }

    /**
     * Check if task is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }

    /**
     * Check if task is expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }
        
        return $this->started_at->addHours(self::TASK_EXPIRY_HOURS)->isPast();
    }

    /**
     * Check if screenshot is uploaded.
     */
    public function hasScreenshot(): bool
    {
        return !is_null($this->screenshot_path) && !is_null($this->screenshot_uploaded_at);
    }

    /**
     * Check if task is pending review.
     */
    public function isPendingReview(): bool
    {
        return in_array($this->status, [self::STATUS_SCREENSHOT_UPLOADED, self::STATUS_PENDING_REVIEW]);
    }

    /**
     * Check if task is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if task is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if user can upload screenshot.
     */
    public function canUploadScreenshot(): bool
    {
        return $this->isActive() && !$this->hasScreenshot();
    }

    /**
     * Check if user can submit appeal.
     */
    public function canSubmitAppeal(): bool
    {
        return $this->isRejected() && is_null($this->appeal_submitted_at);
    }

    /**
     * Get time remaining to upload screenshot.
     */
    public function getTimeRemainingAttribute(): ?Carbon
    {
        if (!$this->isActive()) {
            return null;
        }
        
        $deadline = $this->started_at->addHours(self::TASK_EXPIRY_HOURS);
        return $deadline->isFuture() ? $deadline : null;
    }

    /**
     * Get hours remaining to upload screenshot.
     */
    public function getHoursRemainingAttribute(): int
    {
        $timeRemaining = $this->time_remaining;
        return $timeRemaining ? max(0, $timeRemaining->diffInHours(now())) : 0;
    }

    /**
     * Upload screenshot.
     */
    public function uploadScreenshot(string $screenshotPath, int $viewCount): bool
    {
        if (!$this->canUploadScreenshot()) {
            return false;
        }
        
        return $this->update([
            'screenshot_path' => $screenshotPath,
            'screenshot_uploaded_at' => now(),
            'view_count' => $viewCount,
            'status' => self::STATUS_PENDING_REVIEW,
        ]);
    }

    /**
     * Mark as expired.
     */
    public function markAsExpired(): bool
    {
        return $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Approve task.
     */
    public function approve(User $admin): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by_admin_id' => $admin->id,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject task.
     */
    public function reject(User $admin, string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by_admin_id' => $admin->id,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Submit appeal.
     */
    public function submitAppeal(string $message): bool
    {
        if (!$this->canSubmitAppeal()) {
            return false;
        }
        
        return $this->update([
            'appeal_message' => $message,
            'appeal_submitted_at' => now(),
            'appeal_status' => self::APPEAL_STATUS_PENDING,
        ]);
    }

    /**
     * Approve appeal.
     */
    public function approveAppeal(User $admin): bool
    {
        return $this->update([
            'appeal_status' => self::APPEAL_STATUS_APPROVED,
            'appeal_reviewed_at' => now(),
            'appeal_reviewed_by_admin_id' => $admin->id,
            'status' => self::STATUS_APPROVED,
        ]);
    }

    /**
     * Reject appeal.
     */
    public function rejectAppeal(User $admin): bool
    {
        return $this->update([
            'appeal_status' => self::APPEAL_STATUS_REJECTED,
            'appeal_reviewed_at' => now(),
            'appeal_reviewed_by_admin_id' => $admin->id,
        ]);
    }

    /**
     * Calculate earnings based on view count.
     */
    public function calculateEarnings(): float
    {
        if (!$this->view_count || !$this->ad) {
            return 0.0;
        }
        
        return $this->view_count * $this->ad->earnings_per_view;
    }

    /**
     * Add earnings to user's balance.
     */
    protected function addEarningsToUser(): void
    {
        if (!$this->earnings_amount || !$this->user) {
            return;
        }
        
        // Create transaction for earnings
        $this->user->transactions()->create([
            'type' => Transaction::TYPE_CREDIT,
            'category' => Transaction::CATEGORY_AD_EARNINGS,
            'amount' => $this->earnings_amount,
            'description' => "Earnings from ad: {$this->ad->title}",
            'status' => Transaction::STATUS_COMPLETED,
            'reference' => 'AD_EARN_' . $this->id . '_' . time(),
            'related_id' => $this->id,
            'source' => 'ad_task',
            'metadata' => [
                'ad_id' => $this->ad_id,
                'ad_task_id' => $this->id,
                'view_count' => $this->view_count,
                'earnings_per_view' => $this->ad->earnings_per_view,
            ],
        ]);
        
        // Update user's earnings balance
        $this->user->increment('earnings_balance', $this->earnings_amount);
    }

    /**
     * Get screenshot URL.
     */
    public function getScreenshotUrlAttribute(): ?string
    {
        if (!$this->screenshot_path) {
            return null;
        }
        
        if (Str::startsWith($this->screenshot_path, ['http://', 'https://'])) {
            return $this->screenshot_path;
        }
        
        return asset('storage/' . $this->screenshot_path);
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SCREENSHOT_UPLOADED => 'Screenshot Uploaded',
            self::STATUS_PENDING_REVIEW => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
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
            self::STATUS_ACTIVE => 'blue',
            self::STATUS_SCREENSHOT_UPLOADED => 'yellow',
            self::STATUS_PENDING_REVIEW => 'orange',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_EXPIRED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Scope for active tasks.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('started_at', '>', now()->subHours(self::TASK_EXPIRY_HOURS));
    }

    /**
     * Scope for expired tasks.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
                    ->orWhere(function ($q) {
                        $q->where('status', self::STATUS_ACTIVE)
                          ->where('started_at', '<=', now()->subHours(self::TASK_EXPIRY_HOURS));
                    });
    }

    /**
     * Scope for pending review tasks.
     */
    public function scopePendingReview($query)
    {
        return $query->whereIn('status', [self::STATUS_SCREENSHOT_UPLOADED, self::STATUS_PENDING_REVIEW]);
    }

    /**
     * Scope for approved tasks.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected tasks.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for tasks with pending appeals.
     */
    public function scopeWithPendingAppeal($query)
    {
        return $query->where('appeal_status', self::APPEAL_STATUS_PENDING);
    }

    /**
     * Scope for tasks of a specific user.
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for tasks of a specific ad.
     */
    public function scopeOfAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }
}