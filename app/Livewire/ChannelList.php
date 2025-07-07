<?php

namespace App\Livewire;

use App\Models\Channel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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