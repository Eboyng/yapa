<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelSale;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $minAudienceSize = '';
    public $maxAudienceSize = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'minAudienceSize' => ['except' => ''],
        'maxAudienceSize' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingMinAudienceSize()
    {
        $this->resetPage();
    }

    public function updatingMaxAudienceSize()
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
        $this->category = '';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->minAudienceSize = '';
        $this->maxAudienceSize = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function buyNow($channelSaleId)
    {
        return redirect()->route('channel-sale.buy-now', $channelSaleId);
    }

    public function render()
    {
        $query = ChannelSale::query()
            ->with('user')
            ->available()
            ->where('visibility', true);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('channel_name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if ($this->category) {
            $query->category($this->category);
        }

        // Price range filter
        if ($this->minPrice || $this->maxPrice) {
            $query->priceRange($this->minPrice, $this->maxPrice);
        }

        // Audience size filter
        if ($this->minAudienceSize || $this->maxAudienceSize) {
            $query->audienceSize($this->minAudienceSize, $this->maxAudienceSize);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $channelSales = $query->paginate($this->perPage);

        return view('livewire.channel-sale.browse', [
            'channelSales' => $channelSales,
            'categories' => ChannelSale::CATEGORIES,
        ]);
    }
}