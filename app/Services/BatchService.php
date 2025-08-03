<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchMember;
use App\Models\User;
use App\Models\Interest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class BatchService
{
    /**
     * Create a new batch.
     */
    public function createBatch(array $data, ?User $admin = null): Batch
    {
        $batchData = [
            'name' => $data['name'],
            'limit' => $data['limit'] ?? ($data['type'] === Batch::TYPE_TRIAL ? Batch::TRIAL_LIMIT : Batch::REGULAR_LIMIT),
            'location' => $data['location'] ?? null,
            'type' => $data['type'] ?? Batch::TYPE_REGULAR,
            'cost_in_credits' => $data['cost_in_credits'] ?? 0,
            'description' => $data['description'] ?? null,
            'created_by_admin' => !is_null($admin),
            'admin_user_id' => $admin?->id,
        ];

        $batch = Batch::create($batchData);

        // Attach interests if provided
        if (!empty($data['interest_ids'])) {
            $batch->interests()->attach($data['interest_ids']);
        }

        return $batch;
    }

    /**
     * Auto-create trial batch when current one is full.
     */
    public function autoCreateTrialBatch(): ?Batch
    {
        // Check if there's an open trial batch
        $openTrialBatch = Batch::trial()->open()->first();
        
        if ($openTrialBatch && !$openTrialBatch->isFull()) {
            return $openTrialBatch;
        }

        // Create new trial batch only when current one is full
        return $this->createBatch([
            'name' => 'Trial Batch - ' . now()->format('M d, Y H:i'),
            'type' => Batch::TYPE_TRIAL,
            'limit' => Batch::TRIAL_LIMIT,
            'cost_in_credits' => 0,
            'description' => 'Auto-generated trial batch for new users',
        ]);
    }

    /**
     * Get or create available trial batch for new users.
     */
    public function getAvailableTrialBatch(): ?Batch
    {
        // Check if there's an open trial batch with space
        $openTrialBatch = Batch::trial()->open()->first();
        
        if ($openTrialBatch && !$openTrialBatch->isFull()) {
            return $openTrialBatch;
        }
        
        // If current trial batch is full, create a new one
        if ($openTrialBatch && $openTrialBatch->isFull()) {
            $openTrialBatch->markAsFull();
            return $this->autoCreateTrialBatch();
        }
        
        // No trial batch exists, create the first one
        return $this->autoCreateTrialBatch();
    }

    /**
     * Join user to a batch.
     */
    public function joinBatch(User $user, Batch $batch): array
    {
        if (!$batch->canUserJoin($user)) {
            return [
                'success' => false,
                'message' => $this->getJoinErrorMessage($user, $batch),
            ];
        }

        try {
            $batchMember = $user->joinBatch($batch);
            
            if (!$batchMember) {
                return [
                    'success' => false,
                    'message' => 'Failed to join batch. Please try again.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Successfully joined the batch!',
                'batch_member' => $batchMember,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to join batch', [
                'user_id' => $user->id,
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while joining the batch.',
            ];
        }
    }

    /**
     * Get error message for batch join failure.
     */
    protected function getJoinErrorMessage(User $user, Batch $batch): string
    {
        if (!$batch->isOpen()) {
            return 'This batch is no longer open for new members.';
        }

        if ($batch->isFull()) {
            return 'This batch is already full.';
        }

        if ($batch->isExpired()) {
            return 'This batch has expired.';
        }

        if ($batch->members()->where('user_id', $user->id)->exists()) {
            return 'You have already joined this batch.';
        }

        if ($batch->type === Batch::TYPE_REGULAR && !$user->hasSufficientCredits($batch->cost_in_credits)) {
            return "You need {$batch->cost_in_credits} credits to join this batch.";
        }

        if ($batch->type === Batch::TYPE_TRIAL && !$user->hasNeverJoinedAnyBatch()) {
            return 'Trial batches are only available to new users who have never joined any batch before.';
        }

        return 'You cannot join this batch at the moment.';
    }

    /**
     * Generate and export VCF file for batch.
     */
    public function generateVcfFile(Batch $batch): string
    {
        $contacts = $this->getContactsForExport($batch);
        $vcfContent = $this->generateVcfContent($contacts);
        
        $filename = 'batch_' . $batch->id . '_contacts_' . time() . '.vcf';
        $path = 'vcf_exports/' . $filename;
        
        Storage::disk('public')->put($path, $vcfContent);
        
        // Update batch with VCF path
        $batch->update(['download_vcf_path' => $path]);
        
        return Storage::disk('public')->url($path);
    }

    /**
     * Get contacts for export with smart filtering.
     */
    protected function getContactsForExport(Batch $batch): array
    {
        $contacts = [];
        
        // Add admin contact first
        $adminContact = $this->getAdminContact();
        if ($adminContact) {
            $contacts[] = $adminContact;
        }
        
        // Get batch contacts with smart filtering
        $batchContacts = BatchMember::getContactsForExport($batch);
        
        // Apply Google People API deduplication if available
        $batchContacts = $this->deduplicateWithGooglePeople($batchContacts, $batch);
        
        return array_merge($contacts, $batchContacts);
    }

    /**
     * Get admin contact from settings.
     */
    protected function getAdminContact(): ?array
    {
        $adminName = app(SettingService::class)->get('admin_contact_name');
        $adminNumber = app(SettingService::class)->get('admin_contact_number');
        
        if (!$adminName || !$adminNumber) {
            return null;
        }
        
        return [
            'name' => 'yapa_admin',
            'whatsapp_number' => $adminNumber,
            'location' => '',
            'interests' => '',
            'is_admin' => true, // Keep for VCF generation logic
        ];
    }

    /**
     * Deduplicate contacts using Google People API cache.
     */
    protected function deduplicateWithGooglePeople(array $contacts, Batch $batch): array
    {
        $deduplicatedContacts = [];
        $seenNumbers = [];
        
        foreach ($contacts as $contact) {
            $normalizedNumber = $this->normalizePhoneNumber($contact['whatsapp_number']);
            
            if (!in_array($normalizedNumber, $seenNumbers)) {
                // Check against Google People cache for batch members
                if (!$this->isInGooglePeopleCache($normalizedNumber, $batch)) {
                    $deduplicatedContacts[] = $contact;
                    $seenNumbers[] = $normalizedNumber;
                }
            }
        }
        
        return $deduplicatedContacts;
    }

    /**
     * Check if number exists in Google People cache.
     */
    protected function isInGooglePeopleCache(string $number, Batch $batch): bool
    {
        $members = $batch->members()->with('user')->get();
        
        foreach ($members as $member) {
            if ($member->user && $member->user->hasValidGooglePeopleCache()) {
                $cachedContacts = $member->user->getCachedGooglePeopleContacts();
                
                foreach ($cachedContacts as $cachedContact) {
                    if (isset($cachedContact['phoneNumbers'])) {
                        foreach ($cachedContact['phoneNumbers'] as $phoneNumber) {
                            $cachedNumber = $this->normalizePhoneNumber($phoneNumber['value'] ?? '');
                            if ($cachedNumber === $number) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Normalize phone number for comparison.
     */
    protected function normalizePhoneNumber(string $number): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number);
        
        // Convert to international format
        if (strlen($cleaned) === 11 && str_starts_with($cleaned, '0')) {
            $cleaned = '234' . substr($cleaned, 1);
        }
        
        return $cleaned;
    }

    /**
     * Generate VCF content from contacts.
     */
    protected function generateVcfContent(array $contacts): string
    {
        $vcfContent = '';
        $counter = 1;
        
        foreach ($contacts as $contact) {
            $name = $contact['is_admin'] ?? false ? $contact['name'] : 'yapa_' . $counter;
            $phone = $this->formatPhoneForVcf($contact['whatsapp_number']);
            
            $vcfContent .= "BEGIN:VCARD\r\n";
            $vcfContent .= "VERSION:3.0\r\n";
            $vcfContent .= "FN:{$name}\r\n";
            $vcfContent .= "TEL;TYPE=CELL:{$phone}\r\n";
            
            if (!empty($contact['location'])) {
                $vcfContent .= "ADR;TYPE=HOME:;;{$contact['location']};;;;\r\n";
            }
            
            if (!empty($contact['interests'])) {
                $vcfContent .= "NOTE:Interests: {$contact['interests']}\r\n";
            }
            
            $vcfContent .= "END:VCARD\r\n";
            
            if (!($contact['is_admin'] ?? false)) {
                $counter++;
            }
        }
        
        return $vcfContent;
    }

    /**
     * Format phone number for VCF.
     */
    protected function formatPhoneForVcf(string $number): string
    {
        $normalized = $this->normalizePhoneNumber($number);
        return '+' . $normalized;
    }

    /**
     * Close expired batches and refund credits.
     */
    public function closeExpiredBatches(): int
    {
        $expiredBatches = Batch::expired()->get();
        $closedCount = 0;
        
        foreach ($expiredBatches as $batch) {
            if ($this->closeExpiredBatch($batch)) {
                $closedCount++;
            }
        }
        
        return $closedCount;
    }

    /**
     * Close a single expired batch.
     */
    protected function closeExpiredBatch(Batch $batch): bool
    {
        try {
            // Refund credits to members if batch is not full
            if (!$batch->isFull() && $batch->cost_in_credits > 0) {
                $this->refundBatchMembers($batch);
            }
            
            // Notify members about batch closure
            $this->notifyBatchClosure($batch);
            
            // Mark batch as expired
            $batch->markAsExpired();
            
            Log::info('Closed expired batch', ['batch_id' => $batch->id]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to close expired batch', [
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Refund credits to batch members.
     */
    protected function refundBatchMembers(Batch $batch): void
    {
        $members = $batch->members()->with('user')->get();
        
        foreach ($members as $member) {
            if ($member->user && $batch->cost_in_credits > 0) {
                try {
                    // Create refund transaction
                    $member->user->transactions()->create([
                        'type' => Transaction::TYPE_CREDIT,
                        'category' => Transaction::CATEGORY_REFUND,
                        'amount' => $batch->cost_in_credits,
                        'description' => "Refund for expired batch: {$batch->name}",
                        'status' => Transaction::STATUS_COMPLETED,
                        'reference' => 'BATCH_REFUND_' . $batch->id . '_' . $member->user->id . '_' . time(),
                        'related_id' => $batch->id,
                        'source' => 'batch_refund',
                    ]);
                    
                    // Add credits back to wallet
                    $member->user->getCreditWallet()->increment('balance', $batch->cost_in_credits);
                } catch (\Exception $e) {
                    Log::error('Failed to refund batch member', [
                        'batch_id' => $batch->id,
                        'user_id' => $member->user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Notify members about batch closure.
     */
    protected function notifyBatchClosure(Batch $batch): void
    {
        $members = $batch->members()->with('user')->get();
        
        foreach ($members as $member) {
            if ($member->user && $member->user->email) {
                try {
                    Mail::send('emails.batch-closed', [
                        'user' => $member->user,
                        'batch' => $batch,
                        'was_refunded' => $batch->cost_in_credits > 0,
                    ], function ($message) use ($member, $batch) {
                        $message->to($member->user->email, $member->user->name)
                               ->subject("Batch '{$batch->name}' has been closed");
                    });
                } catch (\Exception $e) {
                    Log::error('Failed to send batch closure notification', [
                        'batch_id' => $batch->id,
                        'user_id' => $member->user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Get batch recommendations for user.
     */
    public function getBatchRecommendations(User $user, int $limit = 10): array
    {
        return $user->getRecommendedBatches($limit)->toArray();
    }

    /**
     * Update batch interests.
     */
    public function updateBatchInterests(Batch $batch, array $interestIds): bool
    {
        $batch->interests()->sync($interestIds);
        return true;
    }

    /**
     * Get batch statistics.
     */
    public function getBatchStatistics(): array
    {
        return [
            'total_batches' => Batch::count(),
            'open_batches' => Batch::open()->count(),
            'full_batches' => Batch::where('status', Batch::STATUS_FULL)->count(),
            'trial_batches' => Batch::trial()->count(),
            'regular_batches' => Batch::regular()->count(),
            'total_members' => BatchMember::count(),
            'unique_participants' => BatchMember::distinct('user_id')->count(),
        ];
    }
}