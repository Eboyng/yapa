<?php

namespace App\Livewire;

use App\Models\ChannelAd;
use App\Models\ChannelAdApplication;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChannelAdList extends Component
{
    public string $search = '';
    public string $nicheFilter = '';
    public string $locationFilter = '';
    public int $minBudget = 0;
    public int $maxBudget = 0;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 12;
    public ?int $highlightChannelId = null;
    public int $page = 1;
    public bool $hasMorePages = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'nicheFilter' => ['except' => ''],
        'locationFilter' => ['except' => ''],
        'minBudget' => ['except' => 0],
        'maxBudget' => ['except' => 0],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'highlightChannelId' => ['except' => null],
    ];

    public function mount()
    {
        // Handle channel_id parameter from booking flow
        if (request()->has('channel_id')) {
            $this->highlightChannelId = request()->get('channel_id');
        }
    }

    public function updatingSearch()
    {
        $this->resetPagination();
    }

    public function updatingNicheFilter()
    {
        $this->resetPagination();
    }

    public function updatingLocationFilter()
    {
        $this->resetPagination();
    }

    public function updatingMinBudget()
    {
        $this->resetPagination();
    }

    public function updatingMaxBudget()
    {
        $this->resetPagination();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
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

    public function clearFilters()
    {
        $this->search = '';
        $this->nicheFilter = '';
        $this->locationFilter = '';
        $this->minBudget = 0;
        $this->maxBudget = 0;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPagination();
    }

    /**
     * Book an ad - redirect to channel show page
     */
    public function bookAd($adId)
    {
        return redirect()->route('channels.show', $adId);
    }

    public function render()
    {
        // Get all channel ads up to current page
        $allChannelAds = collect();
        for ($currentPage = 1; $currentPage <= $this->page; $currentPage++) {
            $pageChannelAds = ChannelAd::with(['adminUser'])
                ->whereHas('adminUser', function ($q) {
                    // Only show ads from admin-verified users
                    $q->whereNotNull('email_verified_at');
                })
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('title', 'like', '%' . $this->search . '%')
                              ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->nicheFilter, function ($q) {
                    $q->whereJsonContains('target_niches', $this->nicheFilter);
                })
                ->when($this->locationFilter, function ($q) {
                    $q->whereHas('adminUser', function ($query) {
                        $query->where('location', 'like', '%' . $this->locationFilter . '%');
                    });
                })
                ->active()
                ->when($this->minBudget > 0, function ($q) {
                    $q->where('payment_per_channel', '>=', $this->minBudget);
                })
                ->when($this->maxBudget > 0, function ($q) {
                    $q->where('payment_per_channel', '<=', $this->maxBudget);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->skip(($currentPage - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();
            
            $allChannelAds = $allChannelAds->concat($pageChannelAds);
        }
        
        // Check if there are more pages
        $nextPageChannelAds = ChannelAd::with(['adminUser'])
            ->whereHas('adminUser', function ($q) {
                $q->whereNotNull('email_verified_at');
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->nicheFilter, function ($q) {
                $q->whereJsonContains('target_niches', $this->nicheFilter);
            })
            ->when($this->locationFilter, function ($q) {
                $q->whereHas('adminUser', function ($query) {
                    $query->where('location', 'like', '%' . $this->locationFilter . '%');
                });
            })
            ->active()
            ->when($this->minBudget > 0, function ($q) {
                $q->where('payment_per_channel', '>=', $this->minBudget);
            })
            ->when($this->maxBudget > 0, function ($q) {
                $q->where('payment_per_channel', '<=', $this->maxBudget);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->skip($this->page * $this->perPage)
            ->take(1)
            ->exists();
        
        $this->hasMorePages = $nextPageChannelAds;
        
        $channelAds = $allChannelAds;

        // Channel functionality removed
        $userChannel = null;
        $userApplications = [];



        // Channel highlighting removed
        $highlightedChannel = null;

        // Get unique locations from admin users who have channel ads
        $locations = User::whereHas('channelAds', function ($q) {
            $q->active();
        })
        ->whereNotNull('location')
        ->whereNotNull('email_verified_at')
        ->distinct()
        ->pluck('location')
        ->filter()
        ->sort()
        ->values();

        return view('livewire.channel-ad-list', [
            'channelAds' => $channelAds,
            'userChannel' => $userChannel,
            'userApplications' => $userApplications,
            'highlightedChannel' => $highlightedChannel,
            'niches' => [
                'technology' => 'Technology',
                'lifestyle' => 'Lifestyle',
                'business' => 'Business',
                'entertainment' => 'Entertainment',
                'education' => 'Education',
                'health' => 'Health & Fitness',
                'travel' => 'Travel',
                'food' => 'Food & Cooking',
                'fashion' => 'Fashion & Beauty',
                'sports' => 'Sports',
                'music' => 'Music',
                'gaming' => 'Gaming',
                'news' => 'News & Politics',
                'finance' => 'Finance',
                'automotive' => 'Automotive',
                'real_estate' => 'Real Estate',
                'parenting' => 'Parenting',
                'pets' => 'Pets & Animals',
                'diy' => 'DIY & Crafts',
                'science' => 'Science',
                'art' => 'Art & Design',
                'photography' => 'Photography',
                'comedy' => 'Comedy',
                'spirituality' => 'Spirituality',
                'other' => 'Other'
            ],
            'locations' => $locations,

        ]);
    }
}