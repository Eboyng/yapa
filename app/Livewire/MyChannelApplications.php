<?php

namespace App\Livewire;

use App\Models\ChannelAdApplication;
use App\Models\Channel;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyChannelApplications extends Component
{
    use WithPagination, WithFileUploads;

    public string $statusFilter = '';
    public string $search = '';
    public int $perPage = 10;
    
    // Proof submission
    public $selectedApplication = null;
    public $proofScreenshot;
    public string $proofDescription = '';
    public bool $isSubmittingProof = false;
    
    // Dispute
    public $disputeApplication = null;
    public string $disputeReason = '';
    public bool $isSubmittingDispute = false;

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected $rules = [
        'proofScreenshot' => 'required|image|max:2048',
        'proofDescription' => 'required|string|min:10|max:500',
        'disputeReason' => 'required|string|min:20|max:1000',
    ];

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->statusFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function openProofModal($applicationId)
    {
        $this->selectedApplication = ChannelAdApplication::findOrFail($applicationId);
        $this->proofDescription = '';
        $this->proofScreenshot = null;
        $this->resetValidation();
        $this->dispatch('open-proof-modal');
    }

    public function submitProof()
    {
        if ($this->isSubmittingProof) {
            return;
        }

        $this->isSubmittingProof = true;

        try {
            $this->validate([
                'proofScreenshot' => 'required|image|max:2048',
                'proofDescription' => 'required|string|min:10|max:500',
            ]);

            if (!$this->selectedApplication || !$this->selectedApplication->isApproved()) {
                session()->flash('error', 'Invalid application or application not approved.');
                return;
            }

            // Upload proof screenshot
            $screenshotPath = $this->proofScreenshot->store('proof-screenshots', 'public');

            // Submit proof using the model method
            $this->selectedApplication->submitProof($screenshotPath, $this->proofDescription);

            session()->flash('success', 'Proof submitted successfully! It will be reviewed by our team.');
            
            $this->selectedApplication = null;
            $this->dispatch('close-proof-modal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit proof: ' . $e->getMessage());
        } finally {
            $this->isSubmittingProof = false;
        }
    }

    public function openDisputeModal($applicationId)
    {
        $this->disputeApplication = ChannelAdApplication::findOrFail($applicationId);
        $this->disputeReason = '';
        $this->resetValidation();
        $this->dispatch('open-dispute-modal');
    }

    public function submitDispute()
    {
        if ($this->isSubmittingDispute) {
            return;
        }

        $this->isSubmittingDispute = true;

        try {
            $this->validate([
                'disputeReason' => 'required|string|min:20|max:1000',
            ]);

            if (!$this->disputeApplication || 
                !in_array($this->disputeApplication->status, [ChannelAdApplication::STATUS_APPROVED, ChannelAdApplication::STATUS_RUNNING])) {
                session()->flash('error', 'Invalid application or cannot dispute at this stage.');
                return;
            }

            // Start dispute
            $this->disputeApplication->startDispute($this->disputeReason);

            session()->flash('success', 'Dispute submitted successfully! Our team will review it.');
            
            $this->disputeApplication = null;
            $this->dispatch('close-dispute-modal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit dispute: ' . $e->getMessage());
        } finally {
            $this->isSubmittingDispute = false;
        }
    }

    public function render()
    {
        // Get user's channel
        $userChannel = Channel::where('user_id', Auth::id())->first();
        
        if (!$userChannel) {
            return view('livewire.my-channel-applications', [
                'applications' => collect(),
                'userChannel' => null,
                'stats' => [],
                'statuses' => [],
            ]);
        }

        $query = ChannelAdApplication::with(['channelAd', 'channel'])
            ->where('channel_id', $userChannel->id)
            ->when($this->search, function ($q) {
                $q->whereHas('channelAd', function ($adQuery) {
                    $adQuery->where('title', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        $applications = $query->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total' => ChannelAdApplication::where('channel_id', $userChannel->id)->count(),
            'pending' => ChannelAdApplication::where('channel_id', $userChannel->id)->where('status', ChannelAdApplication::STATUS_PENDING)->count(),
            'approved' => ChannelAdApplication::where('channel_id', $userChannel->id)->where('status', ChannelAdApplication::STATUS_APPROVED)->count(),
            'completed' => ChannelAdApplication::where('channel_id', $userChannel->id)->where('status', ChannelAdApplication::STATUS_COMPLETED)->count(),
            'total_earnings' => ChannelAdApplication::where('channel_id', $userChannel->id)
                ->where('status', ChannelAdApplication::STATUS_COMPLETED)
                ->where('escrow_status', ChannelAdApplication::ESCROW_STATUS_RELEASED)
                ->sum('escrow_amount') * 0.9, // Channel owner gets 90% after admin fee
        ];

        return view('livewire.my-channel-applications', [
            'applications' => $applications,
            'userChannel' => $userChannel,
            'stats' => $stats,
            'statuses' => [
                ChannelAdApplication::STATUS_PENDING => 'Pending',
                ChannelAdApplication::STATUS_APPROVED => 'Approved',
                ChannelAdApplication::STATUS_REJECTED => 'Rejected',
                ChannelAdApplication::STATUS_RUNNING => 'Running',
                ChannelAdApplication::STATUS_COMPLETED => 'Completed',
                ChannelAdApplication::STATUS_DISPUTED => 'Disputed',
            ],
        ]);
    }
}