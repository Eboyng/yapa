<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelSale;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class MyListings extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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

    public function toggleVisibility($listingId)
    {
        $listing = ChannelSale::where('id', $listingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $listing->update([
            'visibility' => !$listing->visibility
        ]);

        session()->flash('success', 'Listing visibility updated successfully.');
    }

    public function removeListing($listingId)
    {
        $listing = ChannelSale::where('id', $listingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($listing->status === ChannelSale::STATUS_SOLD) {
            session()->flash('error', 'Cannot remove a sold listing.');
            return;
        }

        $listing->markAsRemoved();
        session()->flash('success', 'Listing removed successfully.');
    }

    public function relistChannel($listingId)
    {
        $listing = ChannelSale::where('id', $listingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($listing->status === ChannelSale::STATUS_REMOVED) {
            $listing->update([
                'status' => ChannelSale::STATUS_UNDER_REVIEW,
                'visibility' => true,
            ]);
            session()->flash('success', 'Listing resubmitted for review.');
        }
    }

    public function editListing($listingId)
    {
        return redirect()->route('channel-sale.edit', $listingId);
    }

    public function viewPurchases($listingId)
    {
        return redirect()->route('channel-sale.purchases', $listingId);
    }

    public function render()
    {
        $listings = ChannelSale::with(['purchases'])
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('channel_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $statusOptions = [
            '' => 'All Statuses',
            ChannelSale::STATUS_LISTED => 'Listed',
            ChannelSale::STATUS_UNDER_REVIEW => 'Under Review',
            ChannelSale::STATUS_SOLD => 'Sold',
            ChannelSale::STATUS_REMOVED => 'Removed',
        ];

        return view('livewire.channel-sale.my-listings', [
            'listings' => $listings,
            'statusOptions' => $statusOptions,
        ]);
    }
}