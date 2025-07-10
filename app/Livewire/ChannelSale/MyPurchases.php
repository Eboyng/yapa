<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelPurchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyPurchases extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function viewPurchase($purchaseId)
    {
        return redirect()->route('channel-sale.purchase-details', $purchaseId);
    }

    public function confirmPurchase($purchaseId)
    {
        return redirect()->route('channel-sale.buyer-confirm', $purchaseId);
    }

    public function getStatusOptions()
    {
        return [
            ChannelPurchase::STATUS_PENDING => 'Pending',
            ChannelPurchase::STATUS_IN_ESCROW => 'In Escrow',
            ChannelPurchase::STATUS_COMPLETED => 'Completed',
            ChannelPurchase::STATUS_FAILED => 'Failed',
            ChannelPurchase::STATUS_REFUNDED => 'Refunded',
        ];
    }

    public function render()
    {
        $query = ChannelPurchase::query()
            ->with(['channelSale.user', 'escrowTransaction'])
            ->where('buyer_id', Auth::id());

        // Search filter
        if ($this->search) {
            $query->whereHas('channelSale', function ($q) {
                $q->where('channel_name', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $purchases = $query->paginate($this->perPage);

        return view('livewire.channel-sale.my-purchases', [
            'purchases' => $purchases,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}