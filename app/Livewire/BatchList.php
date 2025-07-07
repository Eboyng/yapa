<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Batch;
use App\Models\BatchMember;
use App\Models\Interest;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\SettingService;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BatchList extends Component
{
    use WithPagination;

    public $filters = [
        'location' => '',
        'interests' => [],
        'type' => 'all',
    ];

    public $creditsBalance = 0;
    public $showFilters = false;
    public $isProcessing = false;

    protected $queryString = [
        'filters.location' => ['except' => ''],
        'filters.type' => ['except' => 'all'],
        'filters.interests' => ['except' => []],
    ];

    protected $listeners = [
        'refreshBatches' => '$refresh',
    ];

    public function mount()
    {
        $this->creditsBalance = Auth::user()->credits_balance ?? 0;
    }

    public function updatedFilters()
    {
        $this->resetPage();
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
        $this->resetPage();
    }

    public function joinBatch($batchId)
    {
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
                        $user,
                        $batch->cost_in_credits,
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

                // Refresh user's credits balance
                $this->creditsBalance = $user->fresh()->credits_balance;
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

    public function downloadVcf($batchId)
    {
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
                'category' => 'contact_download',
                'amount' => 0,
                'balance_before' => $user->credits_balance,
                'balance_after' => $user->credits_balance,
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
        $batches = $this->getBatches();
        $interests = $this->getInterests();
        $locations = $this->getLocations();

        return view('livewire.batch-list', [
            'batches' => $batches,
            'interests' => $interests,
            'locations' => $locations,
        ]);
    }

    protected function getBatches()
    {
        $cacheKey = 'batch_list_' . md5(serialize($this->filters) . $this->getPage());
        
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
                $query->whereHas('interests', function ($q) {
                    $q->whereIn('interests.id', $this->filters['interests']);
                });
            }

            if ($this->filters['type'] !== 'all') {
                $query->where('type', $this->filters['type']);
            }

            return $query->paginate(12);
        });
    }

    protected function getOrderByClause($user)
    {
        // Create weighted scoring for batch recommendations
        $locationWeight = app(SettingService::class)->get('match_location_weight', 0.6);
        $interestWeight = app(SettingService::class)->get('match_interest_weight', 0.4);
        
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
            return "Insufficient credits. You need {$batch->cost_in_credits} credits but only have {$user->credits_balance}.";
        }

        if ($batch->type === Batch::TYPE_TRIAL && $user->hasTrialBatchMembership()) {
            return 'You can only join one trial batch. Upgrade to regular batches for unlimited access.';
        }

        return 'Unable to join this batch at the moment.';
    }

    protected function sendJoinNotification(Batch $batch, $user)
    {
        try {
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