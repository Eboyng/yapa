<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Batch;
use App\Models\BatchMember;
use App\Models\BatchShare;
use App\Models\Interest;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\TransactionService;
use App\Services\SettingService;
use App\Services\OtpService;
use App\Services\WalletService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BatchList extends Component
{

    public $filters = [
        'location' => '',
        'interests' => [],
        'type' => 'all',
    ];

    public $creditsBalance = 0;
    public $showFilters = false;
    public $isProcessing = false;
    public $page = 1;
    public $perPage = 12;
    public $hasMorePages = true;

    protected $queryString = [
        'filters.location' => ['except' => ''],
        'filters.type' => ['except' => 'all'],
        'filters.interests' => ['except' => []],
    ];

    protected $listeners = [
        'refreshBatches' => '$refresh',
        'batchShared' => 'handleBatchShared',
    ];

    public function mount()
    {
        if (Auth::check()) {
            $this->creditsBalance = Auth::user()->getCreditWallet()->balance ?? 0;
        } else {
            $this->creditsBalance = 0;
        }
        
        // Handle referral tracking from shared batch links
        $this->handleReferralFromUrl();
        
        // Handle batch_id parameter from shared links
        $this->handleBatchIdFromUrl();
    }
    
    /**
     * Handle referral tracking when user visits a shared batch link
     */
    private function handleReferralFromUrl()
    {
        $referrerId = request()->get('ref');
        
        if ($referrerId && is_numeric($referrerId)) {
            // Store referral in session for later use when user joins a batch
            session(['batch_referral_id' => $referrerId]);
            
            Log::info('Batch referral captured from URL', [
                'referrer_id' => $referrerId,
                'visitor_ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }
    
    /**
     * Handle batch_id parameter from shared batch links
     */
    private function handleBatchIdFromUrl()
    {
        $batchId = request()->get('batch_id');
        
        if ($batchId && is_numeric($batchId)) {
            // Store batch ID in session to highlight or focus on this batch
            session(['highlighted_batch_id' => $batchId]);
            
            Log::info('Batch ID captured from shared URL', [
                'batch_id' => $batchId,
                'visitor_ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    public function getSettingServiceProperty()
    {
        return app(SettingService::class);
    }

    public function updatedFilters()
    {
        $this->resetPagination();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->filters = [
            'location' => '',
            'interests' => [],
            'type' => 'all',
        ];
        $this->resetPagination();
    }

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->page++;
        }
    }

    public function resetPagination()
    {
        $this->page = 1;
        $this->hasMorePages = true;
    }

    public function joinBatch($batchId)
    {
        if (!Auth::check()) {
            $this->addError('batch_join', 'You must be logged in to join a batch.');
            return;
        }

        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $user = Auth::user();
            $batch = Batch::with(['interests', 'members'])->findOrFail($batchId);

            // Validate user eligibility
            if (!$batch->canUserJoin($user)) {
                $this->addError('batch_join', $this->getJoinErrorMessage($batch, $user));
                return;
            }

            DB::transaction(function () use ($batch, $user) {
                // For regular batches, debit credits
                if ($batch->type === Batch::TYPE_REGULAR && $batch->cost_in_credits > 0) {
                    $transactionService = app(TransactionService::class);
                    $transactionService->debit(
                        $user->id,
                        $batch->cost_in_credits,
                        Wallet::TYPE_CREDITS,
                        Transaction::CATEGORY_BATCH_JOIN,
                        "Joined batch: {$batch->name}",
                        $batch->id
                    );
                }

                // Add user to batch
                BatchMember::create([
                    'batch_id' => $batch->id,
                    'user_id' => $user->id,
                    'whatsapp_number' => $user->whatsapp_number,
                ]);

                // Handle referral tracking if user joined through a shared link
                $this->handleReferralTracking($batch->id, $user->id);

                // If this is a trial batch and it becomes full, auto-create a new one
                if ($batch->type === Batch::TYPE_TRIAL && $batch->isFull()) {
                    $batch->markAsFull();
                    app(\App\Services\BatchService::class)->autoCreateTrialBatch();
                }

                // Refresh user's credits balance
                $this->creditsBalance = $user->fresh()->getCreditWallet()->balance;
            });

            // Send notification
            $this->sendJoinNotification($batch, $user);

            session()->flash('success', "Successfully joined '{$batch->name}'! You'll be notified when the batch is full.");
            $this->dispatch('refreshBatches');

        } catch (\Exception $e) {
            Log::error('Failed to join batch', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            $this->addError('batch_join', 'Failed to join batch. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function shareBatch($batchId, $platform)
    {
        if (!Auth::check()) {
            $this->addError('batch_share', 'You must be logged in to share a batch.');
            return;
        }

        try {
            $user = Auth::user();
            $batch = Batch::findOrFail($batchId);

            // Validate batch is available for sharing
            if (!$batch->isOpen() || $batch->isFull()) {
                $this->addError('batch_share', 'This batch is no longer available for sharing.');
                return;
            }

            // Create or update batch share record
            $batchShare = BatchShare::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'batch_id' => $batch->id,
                    'platform' => $platform,
                ],
                [
                    'share_count' => 0,
                    'rewarded' => false,
                ]
            );

            // Generate sharing URL with batch ID and user ID as referral
            $shareUrl = route('batch.share', ['batch' => $batch->id]) . "?ref={$user->id}";

            // Dispatch event with sharing data
            $this->dispatch('batchShared', [
                'platform' => $platform,
                'url' => $shareUrl,
                'batchName' => $batch->name,
                'batchId' => $batch->id,
            ]);

            session()->flash('success', "Batch shared successfully via {$platform}!");

        } catch (\Exception $e) {
            Log::error('Failed to share batch', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            $this->addError('batch_share', 'Failed to share batch. Please try again.');
        }
    }

    public function handleBatchShared($data)
    {
        // This method handles the client-side sharing completion
        // Additional tracking or analytics can be added here
    }

    public function checkAndRewardShares()
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $user = Auth::user();
            $eligibleShares = BatchShare::where('user_id', $user->id)
                ->eligibleForReward()
                ->with('batch')
                ->get();

            foreach ($eligibleShares as $share) {
                // Calculate reward amount
                $rewardAmount = app(SettingService::class)->get('batch_share_reward_amount', 100);

                // Credit the reward using WalletService
                $walletService = app(WalletService::class);
                $walletService->credit($user, $rewardAmount, 'batch_share_reward');

                // Mark reward as claimed
                $share->update(['rewarded' => true]);

                Log::info('Batch share reward credited', [
                    'user_id' => $user->id,
                    'batch_id' => $share->batch_id,
                    'platform' => $share->platform,
                    'share_count' => $share->share_count,
                    'reward_amount' => $rewardAmount,
                ]);
            }

            if ($eligibleShares->count() > 0) {
                // Refresh user's credits balance
                $this->creditsBalance = $user->fresh()->getCreditWallet()->balance;
                session()->flash('success', "Congratulations! You've earned rewards for your successful batch shares!");
            }

        } catch (\Exception $e) {
            Log::error('Failed to process batch share rewards', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle referral tracking when a user joins a batch
     */
    private function handleReferralTracking($batchId, $newUserId)
    {
        try {
            // Check if there's a referral ID in the session
            $referrerId = session('batch_referral_id');
            
            if (!$referrerId || $referrerId == $newUserId) {
                return; // No referral or self-referral
            }

            // Find the batch share record for the referrer
            $batchShare = BatchShare::where('user_id', $referrerId)
                ->where('batch_id', $batchId)
                ->where('rewarded', false)
                ->first();

            if (!$batchShare) {
                return; // No share record found
            }

            // Increment share count
            $batchShare->incrementShareCount();

            // Check if reward should be given
            if ($batchShare->share_count >= 10 && !$batchShare->rewarded) {
                $this->processShareReward($batchShare);
            }

            // Clear the referral from session
            session()->forget('batch_referral_id');

            Log::info('Batch referral tracked successfully', [
                'batch_id' => $batchId,
                'referrer_id' => $referrerId,
                'new_user_id' => $newUserId,
                'new_share_count' => $batchShare->share_count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle referral tracking', [
                'batch_id' => $batchId,
                'new_user_id' => $newUserId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process the share reward for reaching 10 referrals
     */
    private function processShareReward(BatchShare $batchShare)
    {
        try {
            $walletService = app(WalletService::class);
            $user = $batchShare->user;
            
            // Credit the user with 100 credits
            $walletService->credit($user, 100, 'batch_share_reward');
            
            // Mark as rewarded
            $batchShare->update(['rewarded' => true]);
            
            // Send notification (optional)
            try {
                $notificationService = app(NotificationService::class);
                $message = "Congratulations! You've earned 100 credits for successfully sharing the batch '{$batchShare->batch->name}' and bringing in 10 new members!";
                $notificationService->send($user, $message, 'batch_share_reward');
            } catch (\Exception $e) {
                Log::warning('Failed to send batch share reward notification', [
                    'user_id' => $user->id,
                    'batch_share_id' => $batchShare->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            Log::info('Batch share reward processed successfully', [
                'user_id' => $user->id,
                'batch_id' => $batchShare->batch_id,
                'share_count' => $batchShare->share_count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to process share reward', [
                'batch_share_id' => $batchShare->id,
                'user_id' => $batchShare->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function downloadVcf($batchId)
    {
        if (!Auth::check()) {
            $this->addError('download', 'You must be logged in to download contacts.');
            return;
        }

        try {
            $user = Auth::user();
            $batch = Batch::with(['members.user', 'interests'])->findOrFail($batchId);

            // Validate user is a member and batch is full
            $membership = $batch->members()->where('user_id', $user->id)->first();
            if (!$membership) {
                $this->addError('download', 'You are not a member of this batch.');
                return;
            }

            if (!$batch->isFull()) {
                $this->addError('download', 'Batch is not yet full. Download will be available when all slots are filled.');
                return;
            }

            // Generate VCF content
            $vcfContent = $this->generateVcfContent($batch, $user);

            // Log download transaction
            Transaction::create([
                'user_id' => $user->id,
                'reference' => Transaction::generateReference(),
                'type' => Transaction::TYPE_CREDIT,
                'category' => Transaction::CATEGORY_CONTACT_DOWNLOAD,
                'amount' => 0,
                'balance_before' => $user->getCreditWallet()->balance,
                'balance_after' => $user->getCreditWallet()->balance,
                'description' => "Downloaded contacts for batch: {$batch->name}",
                'status' => Transaction::STATUS_COMPLETED,
                'related_id' => $batch->id,
                'source' => 'batch_download',
                'completed_at' => now(),
            ]);

            // Mark member as downloaded
            $membership->markAsDownloaded();

            // Return VCF file download
            return Response::streamDownload(function () use ($vcfContent) {
                echo $vcfContent;
            }, "batch_{$batch->id}_contacts.vcf", [
                'Content-Type' => 'text/vcard',
                'Content-Disposition' => 'attachment',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to download VCF', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            $this->addError('download', 'Failed to download contacts. Please try again.');
        }
    }

    public function render()
    {
        $allBatches = collect();
        
        // Load all pages up to current page
        for ($i = 1; $i <= $this->page; $i++) {
            $currentPage = $this->page;
            $this->page = $i;
            $pageBatches = $this->getBatches();
            $this->page = $currentPage;
            
            if ($pageBatches->isNotEmpty()) {
                $allBatches = $allBatches->merge($pageBatches);
            }
        }
        
        $interests = $this->getInterests();
        $locations = $this->getLocations();

        return view('livewire.batch-list', [
            'batches' => $allBatches,
            'interests' => $interests,
            'locations' => $locations,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    protected function getBatches()
    {
        $userId = Auth::id() ?? 'guest';
        $cacheKey = 'batch_list_' . md5(serialize($this->filters) . $this->page . $userId);
        
        return Cache::remember($cacheKey, 300, function () {
            $user = Auth::user();
            $query = Batch::with(['interests', 'members'])
                ->available()
                ->orderByRaw($this->getOrderByClause($user));

            // Apply filters
            if (!empty($this->filters['location'])) {
                $query->where('location', 'like', '%' . $this->filters['location'] . '%');
            }

            if (!empty($this->filters['interests'])) {
                // User has manually selected interest filters
                $query->whereHas('interests', function ($q) {
                    $q->whereIn('interests.id', $this->filters['interests']);
                });
            } elseif ($user && $user->interests()->exists()) {
                // No manual filters, but user has selected interests - prioritize matching batches
                $userInterestIds = $user->interests()->pluck('interests.id')->toArray();
                
                // Use a subquery to prioritize batches with matching interests
                $query->leftJoin('batch_interests', 'batches.id', '=', 'batch_interests.batch_id')
                      ->leftJoin('interests', 'batch_interests.interest_id', '=', 'interests.id')
                      ->selectRaw('batches.*, 
                          CASE WHEN interests.id IN (' . implode(',', $userInterestIds) . ') 
                          THEN 1 ELSE 0 END as has_matching_interest')
                      ->groupBy('batches.id')
                      ->orderByDesc('has_matching_interest');
            }

            if ($this->filters['type'] !== 'all') {
                $query->where('type', $this->filters['type']);
            }

            // Special handling for trial batches - only show to users who have never joined any batch
            if ($this->filters['type'] === 'all' || $this->filters['type'] === Batch::TYPE_TRIAL) {
                if ($user) {
                    $hasNeverJoinedAnyBatch = $user->hasNeverJoinedAnyBatch();
                    
                    if (!$hasNeverJoinedAnyBatch) {
                        // If user has joined any batch before, hide all trial batches
                        $query->where('type', '!=', Batch::TYPE_TRIAL);
                    } else {
                        // If user has never joined any batch, show only one trial batch
                        $query->where(function ($q) {
                            $q->where('type', '!=', Batch::TYPE_TRIAL)
                              ->orWhere(function ($subQ) {
                                  $subQ->where('type', Batch::TYPE_TRIAL)
                                       ->orderBy('created_at', 'desc')
                                       ->limit(1);
                              });
                        });
                    }
                } else {
                    // For guest users, show trial batches to encourage registration
                    // Show all batches including trial ones
                }
            }

            $batches = $query->skip(($this->page - 1) * $this->perPage)
                           ->take($this->perPage + 1)
                           ->get();
            
            $this->hasMorePages = $batches->count() > $this->perPage;
            
            if ($this->hasMorePages) {
                $batches = $batches->take($this->perPage);
            }
            
            return $batches;
        });
    }

    protected function getOrderByClause($user)
    {
        // Create weighted scoring for batch recommendations
        $locationWeight = app(SettingService::class)->get('match_location_weight', 0.6);
        $interestWeight = app(SettingService::class)->get('match_interest_weight', 0.4);
        
        if (!$user) {
            // For guest users, use simple ordering
            return "CASE WHEN type = 'trial' THEN 0 ELSE 1 END, created_at DESC";
        }
        
        $userLocation = $user->location ? "'" . addslashes($user->location) . "'" : "''";
        $userInterestIds = $user->interests()->pluck('interests.id')->toArray();
        $interestIdsStr = empty($userInterestIds) ? '0' : implode(',', $userInterestIds);

        return "
            (
                CASE 
                    WHEN LOWER(location) = LOWER({$userLocation}) THEN {$locationWeight}
                    WHEN location LIKE CONCAT('%', SUBSTRING_INDEX({$userLocation}, ',', -1), '%') THEN " . ($locationWeight * 0.5) . "
                    ELSE 0
                END +
                (
                    SELECT COUNT(*) * {$interestWeight} / 
                    GREATEST(
                        (SELECT COUNT(*) FROM batch_interests WHERE batch_id = batches.id),
                        (SELECT COUNT(*) FROM user_interests WHERE user_id = {$user->id}),
                        1
                    )
                    FROM batch_interests bi 
                    WHERE bi.batch_id = batches.id 
                    AND bi.interest_id IN ({$interestIdsStr})
                )
            ) DESC,
            CASE WHEN type = 'trial' THEN 0 ELSE 1 END,
            created_at DESC
        ";
    }

    protected function getInterests()
    {
        return Cache::remember('interests_active', 3600, function () {
            return Interest::active()->ordered()->get();
        });
    }

    protected function getLocations()
    {
        return Cache::remember('batch_locations', 1800, function () {
            return Batch::available()
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct()
                ->pluck('location')
                ->sort()
                ->values();
        });
    }

    protected function getJoinErrorMessage(Batch $batch, $user)
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
            return "Insufficient credits. You need {$batch->cost_in_credits} credits but only have {$user->getCreditWallet()->balance}.";
        }

        if ($batch->type === Batch::TYPE_TRIAL && !$user->hasNeverJoinedAnyBatch()) {
            return 'Trial batches are only available to new users who have never joined any batch before.';
        }

        return 'Unable to join this batch at the moment.';
    }

    protected function sendJoinNotification(Batch $batch, $user)
    {
        try {
            // Skip notification if user doesn't have WhatsApp number
            if (!$user->whatsapp_number) {
                Log::info('Skipping WhatsApp notification - no WhatsApp number set', [
                    'user_id' => $user->id,
                    'batch_id' => $batch->id
                ]);
                return;
            }
            
            $otpService = app(OtpService::class);
            $message = "Welcome to '{$batch->name}'! You'll be notified when the batch is full and ready for download. Happy networking! 🎉";
            
            $result = $otpService->sendWhatsAppMessage($user->whatsapp_number, $message);
            
            if (!$result['success']) {
                Log::warning('WhatsApp notification failed', [
                    'user_id' => $user->id,
                    'batch_id' => $batch->id,
                    'error' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send join notification', [
                'user_id' => $user->id,
                'batch_id' => $batch->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function generateVcfContent(Batch $batch, $user)
    {
        $contacts = [];
        
        // Add admin contact
        $settingService = app(SettingService::class);
        $adminName = $settingService->get('admin_contact_name', 'YAPA Admin');
        $adminNumber = $settingService->get('admin_contact_number', '+2348000000000');
        
        $contacts[] = [
            'name' => $adminName,
            'phone' => $adminNumber,
            'organization' => 'YAPA',
        ];

        // Get batch members
        $members = $batch->members()->with('user')->get();
        $counter = 1;
        
        foreach ($members as $member) {
            $contacts[] = [
                'name' => "yapa_{$counter}",
                'phone' => $member->formatted_whatsapp_number,
                'organization' => 'YAPA Batch',
                'note' => $member->user->location ?? '',
            ];
            $counter++;
        }

        // Simple deduplication to remove duplicate phone numbers
        $contacts = $this->deduplicateContacts($contacts);

        // Generate VCF content
        $vcfContent = "";
        foreach ($contacts as $contact) {
            $vcfContent .= "BEGIN:VCARD\r\n";
            $vcfContent .= "VERSION:3.0\r\n";
            $vcfContent .= "FN:{$contact['name']}\r\n";
            $vcfContent .= "TEL;TYPE=CELL:{$contact['phone']}\r\n";
            
            if (!empty($contact['organization'])) {
                $vcfContent .= "ORG:{$contact['organization']}\r\n";
            }
            
            if (!empty($contact['note'])) {
                $vcfContent .= "NOTE:{$contact['note']}\r\n";
            }
            
            $vcfContent .= "END:VCARD\r\n";
        }

        return $vcfContent;
    }

    protected function deduplicateContacts(array $contacts)
    {
        // Simple deduplication based on phone numbers only
        $seenNumbers = [];
        $uniqueContacts = [];
        
        foreach ($contacts as $contact) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $contact['phone']);
            
            if (!in_array($cleanNumber, $seenNumbers)) {
                $seenNumbers[] = $cleanNumber;
                $uniqueContacts[] = $contact;
            }
        }
        
        return $uniqueContacts;
    }
}