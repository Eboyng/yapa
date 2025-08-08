<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelSale;
use Livewire\Component;

class Browse extends Component
{
    public $search = '';
    public $category = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $minAudienceSize = '';
    public $maxAudienceSize = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;
    public $loadedItems = 12;
    public $hasMoreItems = true;
    public $loading = false;

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
        $this->resetItems();
    }

    public function updatingCategory()
    {
        $this->resetItems();
    }

    public function updatingMinPrice()
    {
        $this->resetItems();
    }

    public function updatingMaxPrice()
    {
        $this->resetItems();
    }

    public function updatingMinAudienceSize()
    {
        $this->resetItems();
    }

    public function updatingMaxAudienceSize()
    {
        $this->resetItems();
    }

    public function resetItems()
    {
        $this->loadedItems = $this->perPage;
        $this->hasMoreItems = true;
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetItems();
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
        $this->resetItems();
    }

    public function loadMore()
    {
        if ($this->loading || !$this->hasMoreItems) {
            return;
        }

        $this->loading = true;
        $this->loadedItems += $this->perPage;
        $this->loading = false;
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

        // Get total count for hasMoreItems check
        $totalCount = $query->count();
        $this->hasMoreItems = $totalCount > $this->loadedItems;

        // Get limited results
        $channelSales = $query->take($this->loadedItems)->get();

        return view('livewire.channel-sale.browse', [
            'channelSales' => $channelSales,
            'categories' => ChannelSale::CATEGORIES,
        ]);
    }
}