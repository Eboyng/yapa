<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'whatsapp_number',
        'password',
        'credits_balance',
        'naira_balance',
        'earnings_balance',
        'location',
        'bvn',
        'whatsapp_verified_at',
        'email_verification_enabled',
        'otp_attempts',
        'otp_expires_at',
        'pending_whatsapp_number',
        'ad_rejection_count',
        'is_flagged_for_ads',
        'flagged_at',
        'appeal_message',
        'appeal_submitted_at',
        'google_people_cache',
        'google_people_cached_at',
        'whatsapp_notifications_enabled',
        'email_notifications_enabled',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'bvn',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'whatsapp_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'credits_balance' => 'integer',
            'naira_balance' => 'decimal:2',
            'earnings_balance' => 'decimal:2',
            'email_verification_enabled' => 'boolean',
            'otp_attempts' => 'integer',
            'ad_rejection_count' => 'integer',
            'is_flagged_for_ads' => 'boolean',
            'flagged_at' => 'datetime',
            'appeal_submitted_at' => 'datetime',
            'google_people_cache' => 'array',
            'google_people_cached_at' => 'datetime',
            'whatsapp_notifications_enabled' => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the user's interests.
     */
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'user_interests')
            ->withTimestamps();
    }

    /**
     * Get the user's wallets.
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get the user's transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the user's audit logs as admin.
     */
    public function adminAuditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'admin_user_id');
    }

    /**
     * Get the user's audit logs as target.
     */
    public function targetAuditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'target_user_id');
    }

    /**
     * Get the user's batch memberships.
     */
    public function batchMemberships(): HasMany
    {
        return $this->hasMany(BatchMember::class);
    }

    /**
     * Get the user's batches through memberships.
     */
    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_members')
            ->withPivot(['whatsapp_number', 'joined_at', 'notified_at', 'downloaded_at'])
            ->withTimestamps();
    }

    /**
     * Get the user's ad tasks.
     */
    public function adTasks(): HasMany
    {
        return $this->hasMany(AdTask::class);
    }

    /**
     * Get the user's channels.
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's notification logs.
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Get the user's active ad tasks.
     */
    public function activeAdTasks(): HasMany
    {
        return $this->hasMany(AdTask::class)->where('status', AdTask::STATUS_ACTIVE);
    }

    /**
     * Get the user's approved ad tasks.
     */
    public function approvedAdTasks(): HasMany
    {
        return $this->hasMany(AdTask::class)->where('status', AdTask::STATUS_APPROVED);
    }

    /**
     * Get or create wallet for specific type.
     */
    public function getWallet(string $type): Wallet
    {
        return Wallet::getOrCreate($this->id, $type);
    }

    /**
     * Get credit wallet.
     */
    public function getCreditWallet(): Wallet
    {
        return $this->getWallet(Wallet::TYPE_CREDITS);
    }

    /**
     * Get naira wallet.
     */
    public function getNairaWallet(): Wallet
    {
        return $this->getWallet(Wallet::TYPE_NAIRA);
    }

    /**
     * Get earnings wallet.
     */
    public function getEarningsWallet(): Wallet
    {
        return $this->getWallet(Wallet::TYPE_EARNINGS);
    }

    /**
     * Check if user has sufficient credits.
     */
    public function hasSufficientCredits(int $amount): bool
    {
        return $this->getCreditWallet()->hasSufficientBalance($amount);
    }

    /**
     * Check if user has sufficient naira balance.
     */
    public function hasSufficientNaira(float $amount): bool
    {
        return $this->getNairaWallet()->hasSufficientBalance($amount);
    }

    /**
     * Encrypt and set BVN.
     */
    public function setBvnAttribute(?string $value): void
    {
        $this->attributes['bvn'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt and get BVN.
     */
    public function getBvnAttribute(?string $value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Check if WhatsApp is verified.
     */
    public function hasVerifiedWhatsApp(): bool
    {
        return !is_null($this->whatsapp_verified_at);
    }

    /**
     * Check if email verification is enabled.
     */
    public function isEmailVerificationEnabled(): bool
    {
        return $this->email_verification_enabled;
    }

    /**
     * Check if OTP has expired.
     */
    public function hasOtpExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    /**
     * Check if max OTP attempts reached.
     */
    public function hasMaxOtpAttempts(): bool
    {
        return $this->otp_attempts >= 3;
    }

    /**
     * Reset OTP attempts.
     */
    public function resetOtpAttempts(): bool
    {
        return $this->update([
            'otp_attempts' => 0,
            'otp_expires_at' => null,
        ]);
    }

    /**
     * Increment OTP attempts.
     */
    public function incrementOtpAttempts(): bool
    {
        return $this->increment('otp_attempts');
    }

    /**
     * Set OTP expiration.
     */
    public function setOtpExpiration(int $minutes = 5): bool
    {
        return $this->update([
            'otp_expires_at' => Carbon::now()->addMinutes($minutes),
        ]);
    }

    /**
     * Mark WhatsApp as verified.
     */
    public function markWhatsAppAsVerified(): bool
    {
        return $this->update([
            'whatsapp_verified_at' => Carbon::now(),
            'otp_attempts' => 0,
            'otp_expires_at' => null,
        ]);
    }

    /**
     * Check if user can change WhatsApp number.
     */
    public function canChangeWhatsAppNumber(): bool
    {
        return $this->hasSufficientCredits(100) && !$this->pending_whatsapp_number;
    }

    /**
     * Get formatted credit balance.
     */
    public function getFormattedCreditsAttribute(): string
    {
        return number_format($this->getCreditWallet()->balance, 0) . ' credits';
    }

    /**
     * Get formatted naira balance.
     */
    public function getFormattedNairaAttribute(): string
    {
        return '₦' . number_format($this->getNairaWallet()->balance, 2);
    }

    /**
     * Get formatted earnings balance.
     */
    public function getFormattedEarningsAttribute(): string
    {
        return '₦' . number_format($this->getEarningsWallet()->balance, 2);
    }

    /**
     * Scope to find by email or WhatsApp number.
     */
    public function scopeFindByEmailOrWhatsApp($query, string $identifier)
    {
        return $query->where('email', $identifier)
                    ->orWhere('whatsapp_number', $identifier);
    }

    /**
     * Scope for verified WhatsApp users.
     */
    public function scopeWhatsAppVerified($query)
    {
        return $query->whereNotNull('whatsapp_verified_at');
    }

    /**
     * Scope for users with email verification enabled.
     */
    public function scopeEmailVerificationEnabled($query)
    {
        return $query->where('email_verification_enabled', true);
    }

    /**
     * Check if user has an active ad task.
     */
    public function hasActiveAdTask(): bool
    {
        return $this->activeAdTasks()->exists();
    }

    /**
     * Check if user is flagged for ads.
     */
    public function isFlaggedForAds(): bool
    {
        return $this->is_flagged_for_ads;
    }

    /**
     * Check if user can submit ad appeal.
     */
    public function canSubmitAdAppeal(): bool
    {
        return $this->is_flagged_for_ads && is_null($this->appeal_submitted_at);
    }

    /**
     * Increment ad rejection count and flag if necessary.
     */
    public function incrementAdRejectionCount(): bool
    {
        $this->increment('ad_rejection_count');
        
        // Flag user after 3 rejections
        if ($this->ad_rejection_count >= 3 && !$this->is_flagged_for_ads) {
            return $this->update([
                'is_flagged_for_ads' => true,
                'flagged_at' => now(),
            ]);
        }
        
        return true;
    }

    /**
     * Submit appeal for ad flagging.
     */
    public function submitAdAppeal(string $message): bool
    {
        if (!$this->canSubmitAdAppeal()) {
            return false;
        }
        
        return $this->update([
            'appeal_message' => $message,
            'appeal_submitted_at' => now(),
        ]);
    }

    /**
     * Unflag user for ads.
     */
    public function unflagForAds(): bool
    {
        return $this->update([
            'is_flagged_for_ads' => false,
            'flagged_at' => null,
            'ad_rejection_count' => 0,
            'appeal_message' => null,
            'appeal_submitted_at' => null,
        ]);
    }

    /**
     * Check if user has trial batch membership.
     */
    public function hasTrialBatchMembership(): bool
    {
        return $this->batchMemberships()
            ->whereHas('batch', function ($query) {
                $query->where('type', Batch::TYPE_TRIAL);
            })
            ->exists();
    }

    /**
     * Get user's total ad earnings.
     */
    public function getTotalAdEarnings(): float
    {
        return $this->approvedAdTasks()->sum('earnings_amount') ?? 0.0;
    }

    /**
     * Get recommended batches for user.
     */
    public function getRecommendedBatches(int $limit = 10)
    {
        return Batch::available()
            ->matchingUser($this)
            ->get()
            ->map(function ($batch) {
                $batch->match_score = $batch->calculateMatchScore($this);
                return $batch;
            })
            ->sortByDesc('match_score')
            ->take($limit);
    }

    /**
     * Update user interests and trigger batch recommendation re-evaluation.
     */
    public function updateInterests(array $interestIds): bool
    {
        $this->interests()->sync($interestIds);
        
        // Trigger batch recommendation re-evaluation
        // This could be done via a job or event
        event(new \App\Events\UserInterestsUpdated($this));
        
        return true;
    }

    /**
     * Check if Google People cache is valid.
     */
    public function hasValidGooglePeopleCache(): bool
    {
        return $this->google_people_cached_at &&
               $this->google_people_cached_at->isAfter(now()->subHours(24)) &&
               !is_null($this->google_people_cache);
    }

    /**
     * Update Google People cache.
     */
    public function updateGooglePeopleCache(array $contacts): bool
    {
        return $this->update([
            'google_people_cache' => $contacts,
            'google_people_cached_at' => now(),
        ]);
    }

    /**
     * Get cached Google People contacts.
     */
    public function getCachedGooglePeopleContacts(): array
    {
        return $this->hasValidGooglePeopleCache() ? $this->google_people_cache : [];
    }

    /**
     * Join a batch.
     */
    public function joinBatch(Batch $batch): ?BatchMember
    {
        if (!$batch->canUserJoin($this)) {
            return null;
        }
        
        // Deduct credits for regular batches
        if ($batch->type === Batch::TYPE_REGULAR && $batch->cost_in_credits > 0) {
            $creditWallet = $this->getCreditWallet();
            if (!$creditWallet->hasSufficientBalance($batch->cost_in_credits)) {
                return null;
            }
            
            // Create debit transaction
            $this->transactions()->create([
                'wallet_id' => $creditWallet->id,
                'type' => Transaction::TYPE_DEBIT,
                'category' => Transaction::CATEGORY_BATCH_JOIN,
                'amount' => $batch->cost_in_credits,
                'description' => "Joined batch: {$batch->name}",
                'status' => Transaction::STATUS_COMPLETED,
                'reference' => 'BATCH_JOIN_' . $batch->id . '_' . time(),
                'related_id' => $batch->id,
                'source' => 'batch_join',
                'balance_before' => $creditWallet->balance,
                'balance_after' => $creditWallet->balance - $batch->cost_in_credits,
            ]);
            
            $creditWallet->decrement('balance', $batch->cost_in_credits);
        }
        
        // Create batch membership
        return $this->batchMemberships()->create([
            'batch_id' => $batch->id,
            'whatsapp_number' => $this->whatsapp_number,
        ]);
    }

    /**
     * Scope for flagged users.
     */
    public function scopeFlaggedForAds($query)
    {
        return $query->where('is_flagged_for_ads', true);
    }

    /**
     * Scope for users with pending ad appeals.
     */
    public function scopeWithPendingAdAppeal($query)
    {
        return $query->where('is_flagged_for_ads', true)
                    ->whereNotNull('appeal_submitted_at');
    }

    /**
     * Scope for users by location.
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', '%' . $location . '%');
    }

    /**
     * Scope for users with specific interests.
     */
    public function scopeWithInterests($query, array $interestIds)
    {
        return $query->whereHas('interests', function ($q) use ($interestIds) {
            $q->whereIn('interests.id', $interestIds);
        });
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if WhatsApp notifications are enabled.
     */
    public function hasWhatsAppNotificationsEnabled(): bool
    {
        return $this->whatsapp_notifications_enabled ?? true;
    }

    /**
     * Check if email notifications are enabled.
     */
    public function hasEmailNotificationsEnabled(): bool
    {
        return $this->email_notifications_enabled ?? true;
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->notifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Scope for admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }
}
