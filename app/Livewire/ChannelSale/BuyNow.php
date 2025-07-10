<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelSale;
use App\Models\ChannelPurchase;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BuyNow extends Component
{
    public ChannelSale $channelSale;
    public $agreedToTerms = false;
    public $buyerNote = '';
    public $showConfirmation = false;

    protected $rules = [
        'agreedToTerms' => 'required|accepted',
        'buyerNote' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'agreedToTerms.required' => 'You must agree to the terms and conditions.',
        'agreedToTerms.accepted' => 'You must agree to the terms and conditions.',
        'buyerNote.max' => 'Note cannot exceed 500 characters.',
    ];

    public function mount(ChannelSale $channelSale)
    {
        // Ensure the channel is available and visible
        if (!$channelSale->isAvailable() || !$channelSale->visibility) {
            abort(404, 'Channel not found or not available.');
        }

        $this->channelSale = $channelSale->load('user');

        // Prevent users from buying their own channels
        if ($this->channelSale->user_id === Auth::id()) {
            abort(403, 'You cannot purchase your own channel.');
        }

        // Check if user has sufficient balance
        $user = Auth::user();
        if ($user->getNairaWallet()->balance < $this->channelSale->price) {
            session()->flash('error', 'Insufficient balance. Please top up your wallet first.');
        }
    }

    public function showConfirmationModal()
    {
        $this->validate();
        $this->showConfirmation = true;
    }

    public function cancelPurchase()
    {
        $this->showConfirmation = false;
    }

    public function confirmPurchase()
    {
        $this->validate();

        $user = Auth::user();

        // Refresh channel sale to get latest data
        $this->channelSale->refresh();

        // Double-check balance
        if ($user->getNairaWallet()->balance < $this->channelSale->price) {
            session()->flash('error', 'Insufficient balance. Please top up your wallet first.');
            $this->showConfirmation = false;
            return;
        }

        // Check if channel is still available
        if (!$this->channelSale->isAvailable()) {
            session()->flash('error', 'This channel is no longer available for purchase.');
            $this->showConfirmation = false;
            return;
        }

        // Check if user already has a pending purchase for this channel
        $existingPurchase = ChannelPurchase::where('buyer_id', $user->id)
            ->where('channel_sale_id', $this->channelSale->id)
            ->whereIn('status', [ChannelPurchase::STATUS_PENDING, ChannelPurchase::STATUS_IN_ESCROW])
            ->first();

        if ($existingPurchase) {
            session()->flash('error', 'You already have a pending purchase for this channel.');
            $this->showConfirmation = false;
            return;
        }

        try {
            DB::beginTransaction();

            // Create the purchase record
            $purchase = ChannelPurchase::create([
                'buyer_id' => $user->id,
                'channel_sale_id' => $this->channelSale->id,
                'price' => $this->channelSale->price,
                'status' => ChannelPurchase::STATUS_PENDING,
            ]);

            // Create escrow transaction
            $purchase->createEscrowTransaction();

            DB::commit();

            // Close confirmation modal
            $this->showConfirmation = false;

            session()->flash('success', 'Purchase initiated successfully! Funds have been placed in escrow.');
            return redirect()->route('channel-sale.my-purchases');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to process channel purchase', [
                'channel_sale_id' => $this->channelSale->id,
                'buyer_id' => $user->id,
                'price' => $this->channelSale->price,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to process purchase: ' . $e->getMessage());
            $this->showConfirmation = false;
        }
    }

    public function render()
    {
        return view('livewire.channel-sale.buy-now');
    }
}