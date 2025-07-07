<?php

namespace App\Livewire;

use App\Models\ChannelAd;
use App\Models\Channel;
use App\Models\ChannelAdApplication;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ChannelAdList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $nicheFilter = '';
    public string $statusFilter = 'active';
    public int $minBudget = 0;
    public int $maxBudget = 0;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'nicheFilter' => ['except' => ''],
        'statusFilter' => ['except' => 'active'],
        'minBudget' => ['except' => 0],
        'maxBudget' => ['except' => 0],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingNicheFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingMinBudget()
    {
        $this->resetPage();
    }

    public function updatingMaxBudget()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->nicheFilter = '';
        $this->statusFilter = 'active';
        $this->minBudget = 0;
        $this->maxBudget = 0;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function applyToAd($channelAdId)
    {
        try {
            // Get user's approved channel
            $channel = Channel::where('user_id', Auth::id())
                ->where('status', Channel::STATUS_APPROVED)
                ->first();

            if (!$channel) {
                session()->flash('error', 'You need an approved channel to apply for ads.');
                return;
            }

            $channelAd = ChannelAd::findOrFail($channelAdId);

            // Check if channel can apply
            if (!$channelAd->canChannelApply($channel)) {
                session()->flash('error', 'You are not eligible to apply for this ad.');
                return;
            }

            // Create application
            ChannelAdApplication::create([
                'channel_id' => $channel->id,
                'channel_ad_id' => $channelAd->id,
                'status' => ChannelAdApplication::STATUS_PENDING,
                'applied_at' => now(),
                'escrow_amount' => $channelAd->payment_per_channel,
            ]);

            session()->flash('success', 'Application submitted successfully! You will be notified once it\'s reviewed.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to apply: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = ChannelAd::with(['adminUser'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->nicheFilter, function ($q) {
                $q->whereJsonContains('target_niches', $this->nicheFilter);
            })
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter === 'active') {
                    $q->active();
                } else {
                    $q->where('status', $this->statusFilter);
                }
            })
            ->when($this->minBudget > 0, function ($q) {
                $q->where('payment_per_channel', '>=', $this->minBudget);
            })
            ->when($this->maxBudget > 0, function ($q) {
                $q->where('payment_per_channel', '<=', $this->maxBudget);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $channelAds = $query->paginate($this->perPage);

        // Get user's channel for application eligibility
        $userChannel = Channel::where('user_id', Auth::id())
            ->where('status', Channel::STATUS_APPROVED)
            ->first();

        // Get user's applications
        $userApplications = [];
        if ($userChannel) {
            $userApplications = ChannelAdApplication::where('channel_id', $userChannel->id)
                ->pluck('channel_ad_id')
                ->toArray();
        }

        // Get statistics
        $stats = [
            'total_active' => ChannelAd::active()->count(),
            'total_budget' => ChannelAd::active()->sum('budget'),
            'avg_payment' => ChannelAd::active()->avg('payment_per_channel'),
        ];

        return view('livewire.channel-ad-list', [
            'channelAds' => $channelAds,
            'userChannel' => $userChannel,
            'userApplications' => $userApplications,
            'stats' => $stats,
            'niches' => Channel::NICHES,
            'statuses' => [
                'active' => 'Active',
                'draft' => 'Draft',
                'paused' => 'Paused',
                'completed' => 'Completed',
                'expired' => 'Expired',
                'cancelled' => 'Cancelled',
            ],
        ]);
    }
}