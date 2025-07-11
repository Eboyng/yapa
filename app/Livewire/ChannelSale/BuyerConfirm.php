<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelPurchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BuyerConfirm extends Component
{
    public ChannelPurchase $purchase;
    public $confirmationNote = '';
    public $showConfirmation = false;

    protected $rules = [
        'confirmationNote' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'confirmationNote.max' => 'Note cannot exceed 500 characters.',
    ];

    public function mount(ChannelPurchase $purchase)
    {
        // Ensure the purchase belongs to the authenticated user
        if ($purchase->buyer_id !== Auth::id()) {
            abort(403, 'You can only view your own purchases.');
        }

        $this->purchase = $purchase->load(['channelSale.user', 'buyer', 'escrowTransaction']);

        // Only allow confirmation for purchases in escrow
        if (!$this->purchase->isInEscrow()) {
            abort(403, 'This purchase cannot be confirmed at this time.');
        }
    }

    public function showConfirmationModal()
    {
        $this->showConfirmation = true;
    }

    public function cancelConfirmation()
    {
        $this->showConfirmation = false;
    }

    public function confirmReceived()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Verify purchase is still in escrow status
            $this->purchase->refresh();
            if (!$this->purchase->isInEscrow()) {
                throw new \InvalidArgumentException('Purchase is no longer in escrow status');
            }

            // Complete the purchase and release escrow
            $this->purchase->completeAndReleaseEscrow();

            // Add confirmation note if provided
            if ($this->confirmationNote) {
                $this->purchase->update([
                    'admin_note' => ($this->purchase->admin_note ? $this->purchase->admin_note . "\n\n" : '') . 
                                   'Buyer confirmation note: ' . $this->confirmationNote
                ]);
            }

            DB::commit();

            // Close the confirmation modal
            $this->showConfirmation = false;

            session()->flash('success', 'Purchase confirmed successfully! The seller has been paid.');
            return redirect()->route('channel-sale.my-purchases');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to confirm channel purchase', [
                'purchase_id' => $this->purchase->id,
                'buyer_id' => $this->purchase->buyer_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to confirm purchase: ' . $e->getMessage());
            $this->showConfirmation = false;
        }
    }

    public function requestRefund()
    {
        try {
            DB::beginTransaction();

            // Verify purchase is still in escrow status
            $this->purchase->refresh();
            if (!$this->purchase->isInEscrow()) {
                throw new \InvalidArgumentException('Purchase is no longer in escrow status');
            }

            // Prepare refund reason
            $refundReason = 'Buyer requested refund';
            if ($this->confirmationNote) {
                $refundReason .= ': ' . $this->confirmationNote;
            }

            // Refund the purchase
            $this->purchase->refund($refundReason);

            DB::commit();

            session()->flash('success', 'Refund processed successfully. Funds have been returned to your wallet.');
            return redirect()->route('channel-sale.my-purchases');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to process channel purchase refund', [
                'purchase_id' => $this->purchase->id,
                'buyer_id' => $this->purchase->buyer_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.channel-sale.buyer-confirm');
    }
}