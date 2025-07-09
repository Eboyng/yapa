<?php

namespace App\Livewire;

use App\Models\Channel;
use App\Models\ChannelAd;
use App\Models\ChannelAdApplication;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChannelList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $nicheFilter = '';
    public string $statusFilter = '';
    public bool $featuredOnly = false;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'nicheFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'featuredOnly' => ['except' => false],
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

    public function updatingFeaturedOnly()
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
        $this->statusFilter = '';
        $this->featuredOnly = false;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function bookChannel($channelId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to book a channel.');
            return redirect()->route('login');
        }

        $channel = Channel::find($channelId);
        
        if (!$channel || !$channel->isApproved()) {
            session()->flash('error', 'Channel not available for booking.');
            return;
        }

        // Find active channel ads that this channel can apply to
        $availableAds = ChannelAd::active()
            ->where(function ($query) use ($channel) {
                $query->whereNull('target_niches')
                      ->orWhereJsonContains('target_niches', $channel->niche);
            })
            ->where(function ($query) use ($channel) {
                $query->whereNull('min_followers')
                      ->orWhere('min_followers', '<=', $channel->follower_count);
            })
            ->whereDoesntHave('channelAdApplications', function ($query) use ($channel) {
                $query->where('channel_id', $channel->id);
            })
            ->get();

        if ($availableAds->isEmpty()) {
            session()->flash('error', 'No available ad campaigns for this channel at the moment.');
            return;
        }

        // For now, redirect to channel ads page with the channel's niche filter
        return redirect()->route('channel-ads.index', ['niche' => $channel->niche])
                        ->with('success', 'Browse available ad campaigns for your channel.');
    }

    public function getAvailableAdsForChannel($channelId)
    {
        $channel = Channel::find($channelId);
        
        if (!$channel) {
            return collect();
        }

        return ChannelAd::active()
            ->where(function ($query) use ($channel) {
                $query->whereNull('target_niches')
                      ->orWhereJsonContains('target_niches', $channel->niche);
            })
            ->where(function ($query) use ($channel) {
                $query->whereNull('min_followers')
                      ->orWhere('min_followers', '<=', $channel->follower_count);
            })
            ->whereDoesntHave('channelAdApplications', function ($query) use ($channel) {
                $query->where('channel_id', $channel->id);
            })
            ->count();
    }

    public function render()
    {
        $query = Channel::with(['user'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->nicheFilter, function ($q) {
                $q->where('niche', $this->nicheFilter);
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->featuredOnly, function ($q) {
                $q->where('is_featured', true);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $channels = $query->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total' => Channel::count(),
            'approved' => Channel::where('status', Channel::STATUS_APPROVED)->count(),
            'pending' => Channel::where('status', Channel::STATUS_PENDING)->count(),
            'featured' => Channel::where('is_featured', true)->count(),
        ];

        return view('livewire.channel-list', [
            'channels' => $channels,
            'stats' => $stats,
            'niches' => Channel::NICHES,
            'statuses' => [
                Channel::STATUS_PENDING => 'Pending',
                Channel::STATUS_APPROVED => 'Approved',
                Channel::STATUS_REJECTED => 'Rejected',
                Channel::STATUS_SUSPENDED => 'Suspended',
            ],
        ]);
    }
}