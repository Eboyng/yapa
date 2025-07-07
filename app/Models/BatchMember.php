<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BatchMember extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'user_id',
        'whatsapp_number',
        'joined_at',
        'notified_at',
        'downloaded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'datetime',
        'notified_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batchMember) {
            if (is_null($batchMember->joined_at)) {
                $batchMember->joined_at = now();
            }
        });

        static::created(function ($batchMember) {
            // Check if batch is now full and update status
            $batch = $batchMember->batch;
            if ($batch && $batch->getCurrentMemberCount() >= $batch->limit) {
                $batch->markAsFull();
                
                // Trigger notification to all members
                $batchMember->notifyBatchMembers();
            }
        });
    }

    /**
     * Get the batch this member belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the user this member represents.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if member has been notified.
     */
    public function hasBeenNotified(): bool
    {
        return !is_null($this->notified_at);
    }

    /**
     * Check if member has downloaded contacts.
     */
    public function hasDownloaded(): bool
    {
        return !is_null($this->downloaded_at);
    }

    /**
     * Mark as notified.
     */
    public function markAsNotified(): bool
    {
        return $this->update(['notified_at' => now()]);
    }

    /**
     * Mark as downloaded.
     */
    public function markAsDownloaded(): bool
    {
        return $this->update(['downloaded_at' => now()]);
    }

    /**
     * Get formatted WhatsApp number.
     */
    public function getFormattedWhatsappNumberAttribute(): string
    {
        $number = preg_replace('/[^0-9]/', '', $this->whatsapp_number);
        
        // Add country code if not present
        if (!str_starts_with($number, '234') && strlen($number) === 11) {
            $number = '234' . substr($number, 1);
        }
        
        return '+' . $number;
    }

    /**
     * Notify all batch members when batch is full.
     */
    protected function notifyBatchMembers(): void
    {
        $batch = $this->batch;
        if (!$batch || !$batch->isFull()) {
            return;
        }

        // Get all members who haven't been notified
        $unnotifiedMembers = $batch->members()
            ->whereNull('notified_at')
            ->with('user')
            ->get();

        foreach ($unnotifiedMembers as $member) {
            try {
                // Send WhatsApp notification if enabled
                if (config('services.whatsapp.enabled', false)) {
                    $this->sendWhatsAppNotification($member);
                }
                
                // Send email notification
                $this->sendEmailNotification($member);
                
                $member->markAsNotified();
            } catch (\Exception $e) {
                \Log::error('Failed to notify batch member', [
                    'batch_member_id' => $member->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Send WhatsApp notification to member.
     */
    protected function sendWhatsAppNotification(BatchMember $member): void
    {
        $message = "🎉 Great news! Your batch '{$member->batch->name}' is now full and ready for download. " .
                  "Visit your dashboard to download the contact list. Happy networking! 📱";
        
        // Use WhatsApp service to send message
        app('whatsapp')->sendMessage($member->formatted_whatsapp_number, $message);
    }

    /**
     * Send email notification to member.
     */
    protected function sendEmailNotification(BatchMember $member): void
    {
        if (!$member->user || !$member->user->email) {
            return;
        }

        $data = [
            'user' => $member->user,
            'batch' => $member->batch,
            'download_url' => route('batches.download', $member->batch->id),
        ];

        \Mail::send('emails.batch-ready', $data, function ($message) use ($member) {
            $message->to($member->user->email, $member->user->name)
                   ->subject("Your batch '{$member->batch->name}' is ready!");
        });
    }

    /**
     * Scope for members of a specific batch.
     */
    public function scopeOfBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope for members of a specific user.
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for notified members.
     */
    public function scopeNotified($query)
    {
        return $query->whereNotNull('notified_at');
    }

    /**
     * Scope for unnotified members.
     */
    public function scopeUnnotified($query)
    {
        return $query->whereNull('notified_at');
    }

    /**
     * Scope for members who downloaded contacts.
     */
    public function scopeDownloaded($query)
    {
        return $query->whereNotNull('downloaded_at');
    }

    /**
     * Scope for recent members (joined in last X days).
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('joined_at', '>=', now()->subDays($days));
    }

    /**
     * Check if WhatsApp number already exists in any batch.
     */
    public static function whatsappNumberExists(string $whatsappNumber, ?int $excludeBatchId = null): bool
    {
        $query = static::where('whatsapp_number', $whatsappNumber);
        
        if ($excludeBatchId) {
            $query->where('batch_id', '!=', $excludeBatchId);
        }
        
        return $query->exists();
    }

    /**
     * Get the most recent batch membership for a WhatsApp number.
     */
    public static function getMostRecentMembership(string $whatsappNumber): ?self
    {
        return static::where('whatsapp_number', $whatsappNumber)
                    ->orderBy('joined_at', 'desc')
                    ->first();
    }

    /**
     * Get unique WhatsApp numbers from previous batches.
     */
    public static function getUniqueWhatsAppNumbers(?int $excludeBatchId = null): array
    {
        $query = static::select('whatsapp_number')
                      ->distinct();
        
        if ($excludeBatchId) {
            $query->where('batch_id', '!=', $excludeBatchId);
        }
        
        return $query->pluck('whatsapp_number')->toArray();
    }

    /**
     * Get contacts for VCF export with conflict resolution.
     */
    public static function getContactsForExport(Batch $batch): array
    {
        $contacts = [];
        $seenNumbers = [];
        
        // Get current batch members first (highest priority)
        $currentMembers = $batch->members()
            ->with('user')
            ->orderBy('joined_at', 'desc')
            ->get();
        
        foreach ($currentMembers as $member) {
            if (!in_array($member->whatsapp_number, $seenNumbers)) {
                $contacts[] = [
                    'name' => $member->user->name ?? 'Unknown',
                    'whatsapp_number' => $member->whatsapp_number,
                    'location' => $member->user->location ?? '',
                    'interests' => $member->user->interests->pluck('name')->implode(', '),
                    'joined_at' => $member->joined_at,
                ];
                $seenNumbers[] = $member->whatsapp_number;
            }
        }
        
        // If batch is not full, add contacts from previous batches
        if (count($contacts) < $batch->limit) {
            $needed = $batch->limit - count($contacts);
            
            $previousMembers = static::whereNotIn('whatsapp_number', $seenNumbers)
                ->whereHas('batch', function ($query) use ($batch) {
                    $query->where('id', '!=', $batch->id)
                          ->where('type', $batch->type); // Same type batches
                })
                ->with(['user', 'batch'])
                ->orderBy('joined_at', 'desc')
                ->limit($needed)
                ->get();
            
            foreach ($previousMembers as $member) {
                $contacts[] = [
                    'name' => $member->user->name ?? 'Unknown',
                    'whatsapp_number' => $member->whatsapp_number,
                    'location' => $member->user->location ?? '',
                    'interests' => $member->user->interests->pluck('name')->implode(', '),
                    'joined_at' => $member->joined_at,
                ];
            }
        }
        
        return $contacts;
    }
}