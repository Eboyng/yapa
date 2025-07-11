<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Batch;
use App\Models\BatchMember;
use App\Models\BatchShare;
use App\Services\ReferralService;
use App\Services\WalletService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MyBatches extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, active, closed
    public $isProcessing = false;
    
    public function mount()
    {
        // Load open batches for sharing functionality
        $this->loadOpenBatches();
    }
    
    // Batch sharing properties
    public $showBatchShareSection = false;
    public $showSharedBatchesList = false;
    public $openBatches = [];
    public $sharedBatches = [];
    public $selectedBatch = null;
    public $shareUrl = '';
    public $isLoadingBatches = false;
    public $isGeneratingShareUrl = false;

    protected $queryString = [
        'filter' => ['except' => 'all'],
    ];

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function downloadContacts($batchId)
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $user = Auth::user();
            $batch = Batch::with(['members.user'])->findOrFail($batchId);

            // Validate user is a member
            $membership = $batch->members()->where('user_id', $user->id)->first();
            if (!$membership) {
                $this->addError('download', 'You are not a member of this batch.');
                return;
            }

            // Check if batch is full for download
            if (!$batch->isFull()) {
                $this->addError('download', 'Batch is not yet full. Download will be available when all slots are filled.');
                return;
            }

            // Generate VCF content
            $vcfContent = $this->generateVcfContent($batch);

            // Mark member as downloaded if not already
            if (!$membership->downloaded_at) {
                $membership->markAsDownloaded();
            }

            // Return VCF file download
            return Response::streamDownload(function () use ($vcfContent) {
                echo $vcfContent;
            }, "batch_{$batch->id}_contacts.vcf", [
                'Content-Type' => 'text/vcard',
                'Content-Disposition' => 'attachment',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to download VCF from MyBatches', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            $this->addError('download', 'Failed to download contacts. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $batches = $this->getMyBatches();

        return view('livewire.my-batches', [
            'batches' => $batches,
        ]);
    }

    protected function getMyBatches()
    {
        $user = Auth::user();
        $query = Batch::with(['interests', 'members'])
            ->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc');

        // Apply filter
        switch ($this->filter) {
            case 'active':
                $query->whereIn('status', [Batch::STATUS_OPEN, Batch::STATUS_FULL]);
                break;
            case 'closed':
                $query->whereIn('status', [Batch::STATUS_CLOSED, Batch::STATUS_EXPIRED]);
                break;
            // 'all' shows everything
        }

        return $query->paginate(10);
    }

    protected function generateVcfContent(Batch $batch)
    {
        $contacts = [];
        
        // Add admin contact
        $settingService = app(\App\Services\SettingService::class);
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

    // Batch sharing methods
    public function toggleBatchShareSection()
    {
        $this->showBatchShareSection = !$this->showBatchShareSection;
        
        if ($this->showBatchShareSection && empty($this->openBatches)) {
            $this->loadOpenBatches();
        }
    }

    public function toggleSharedBatchesList()
    {
        $this->showSharedBatchesList = !$this->showSharedBatchesList;
        
        if ($this->showSharedBatchesList && empty($this->sharedBatches)) {
            $this->loadSharedBatches();
        }
    }

    public function loadOpenBatches()
    {
        $this->isLoadingBatches = true;
        
        try {
            $user = Auth::user();
            $this->openBatches = Batch::where('status', 'open')
                ->select('id', 'name', 'description', 'limit', 'location')
                ->withCount('members')
                ->latest()
                ->get()
                ->map(function ($batch) {
                    return [
                        'id' => $batch->id,
                        'name' => $batch->name,
                        'description' => $batch->description,
                        'members_count' => $batch->members_count,
                        'limit' => $batch->limit,
                        'location' => $batch->location,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load open batches', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->openBatches = [];
            session()->flash('error', 'Failed to load batch data.');
        } finally {
            $this->isLoadingBatches = false;
        }
    }

    public function loadSharedBatches()
    {
        $this->isLoadingBatches = true;
        
        try {
            $user = Auth::user();
            $this->sharedBatches = BatchShare::with(['batch', 'batch.interests'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('batch_id')
                ->map(function ($shares) {
                    $batch = $shares->first()->batch;
                    $totalShares = $shares->count();
                    $shareCount = $shares->sum('share_count');
                    $rewarded = $shares->where('rewarded', true)->count() > 0;
                    $canClaimReward = $shares->where('rewarded', false)->where('share_count', '>=', 10)->count() > 0;
                    
                    $platformStats = [
                        'whatsapp' => $shares->where('platform', BatchShare::PLATFORM_WHATSAPP)->count(),
                        'facebook' => $shares->where('platform', BatchShare::PLATFORM_FACEBOOK)->count(),
                        'twitter' => $shares->where('platform', BatchShare::PLATFORM_TWITTER)->count(),
                        'copy_link' => $shares->where('platform', BatchShare::PLATFORM_COPY_LINK)->count(),
                    ];
                    
                    return [
                        'batch' => $batch,
                        'total_shares' => $totalShares,
                        'share_count' => $shareCount,
                        'rewarded' => $rewarded,
                        'can_claim_reward' => $canClaimReward,
                        'platform_stats' => $platformStats,
                        'progress_percentage' => min(($shareCount / 10) * 100, 100),
                        'shares_data' => $shares->toArray()
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load shared batches', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to load shared batches data.');
        } finally {
            $this->isLoadingBatches = false;
        }
    }

    /**
     * Handle referral when someone joins a batch through a shared link
     */
    public static function handleBatchJoinReferral($batchId, $referrerId, $newUserId)
    {
        try {
            // Find the batch share record
            $batchShare = BatchShare::where('user_id', $referrerId)
                ->where('batch_id', $batchId)
                ->where('rewarded', false)
                ->first();

            if (!$batchShare) {
                return;
            }

            // Increment share count
            $batchShare->incrementShareCount();

            // Check if reward should be given
            if ($batchShare->share_count >= 10 && !$batchShare->rewarded) {
                static::processShareReward($batchShare);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle batch join referral', [
                'batch_id' => $batchId,
                'referrer_id' => $referrerId,
                'new_user_id' => $newUserId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process the share reward for reaching 10 referrals
     */
    private static function processShareReward(BatchShare $batchShare)
    {
        try {
            $walletService = new WalletService();
            $user = $batchShare->user;
            
            // Credit the user with 100 credits
            $walletService->credit($user, 100, 'batch_share_reward');
            
            // Mark as rewarded
            $batchShare->update(['rewarded' => true]);
            
            // Send notification (optional)
            try {
                $notificationService = new NotificationService();
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

    public function generateBatchShareUrl($batchId)
    {
        $this->isGeneratingShareUrl = true;
        
        try {
            $user = Auth::user();
            $batch = Batch::findOrFail($batchId);
            $this->selectedBatch = $batch;
            $this->shareUrl = $batch->getShareLink($user->getReferralCode());
            
            session()->flash('success', 'Share link generated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to generate batch share URL', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to generate share link.');
        } finally {
            $this->isGeneratingShareUrl = false;
        }
    }

    public function shareBatch($platform)
    {
        if (!$this->selectedBatch || !$this->shareUrl) {
            session()->flash('error', 'Please generate a share link first.');
            return;
        }

        try {
            $user = Auth::user();
            
            // Create share record immediately
             $batchId = is_array($this->selectedBatch) ? $this->selectedBatch['id'] : $this->selectedBatch->id;
             
             // Check if share record already exists for this user and batch
             $existingShare = BatchShare::where('user_id', $user->id)
                 ->where('batch_id', $batchId)
                 ->first();
             
             if (!$existingShare) {
                 BatchShare::create([
                     'user_id' => $user->id,
                     'batch_id' => $batchId,
                     'platform' => $platform,
                     'share_count' => 0,
                     'rewarded' => false,
                 ]);
             }

            $message = "Join this amazing batch on YAPA! {$this->shareUrl}";
            
            switch ($platform) {
                case BatchShare::PLATFORM_WHATSAPP:
                    $whatsappUrl = 'https://wa.me/?text=' . urlencode($message);
                    $this->dispatch('openUrl', $whatsappUrl);
                    break;
                    
                case BatchShare::PLATFORM_FACEBOOK:
                    $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($this->shareUrl);
                    $this->dispatch('openUrl', $facebookUrl);
                    break;
                    
                case BatchShare::PLATFORM_TWITTER:
                    $twitterUrl = 'https://twitter.com/intent/tweet?text=' . urlencode($message);
                    $this->dispatch('openUrl', $twitterUrl);
                    break;
                    
                case BatchShare::PLATFORM_COPY_LINK:
                    $this->dispatch('copyToClipboard', $this->shareUrl);
                    break;
            }
            
            session()->flash('success', 'Batch shared successfully!');
            
        } catch (\Exception $e) {
            Log::error('Failed to share batch', [
                'user_id' => Auth::id(),
                'batch_id' => $this->selectedBatch->id,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to share batch.');
        }
    }

    public function getBatchShareProgress($batchId)
    {
        try {
            $user = Auth::user();
            return $user->getBatchShareProgress($batchId);
        } catch (\Exception $e) {
            Log::error('Failed to get batch share progress', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}