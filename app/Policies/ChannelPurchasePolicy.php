<?php

namespace App\Policies;

use App\Models\ChannelPurchase;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelPurchasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Users can view their own purchases or sales
        return $channelPurchase->buyer_id === $user->id || 
               $channelPurchase->channelSale->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only the buyer can update their purchase (e.g., add notes)
        return $channelPurchase->buyer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only allow deletion if purchase is pending and user is the buyer
        return $channelPurchase->buyer_id === $user->id && 
               $channelPurchase->isPending();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChannelPurchase $channelPurchase): bool
    {
        return $channelPurchase->buyer_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChannelPurchase $channelPurchase): bool
    {
        return $channelPurchase->buyer_id === $user->id;
    }

    /**
     * Determine whether the user can confirm receipt of the channel.
     */
    public function confirmReceipt(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only the buyer can confirm receipt, and only if purchase is in escrow
        return $channelPurchase->buyer_id === $user->id && 
               $channelPurchase->isInEscrow();
    }

    /**
     * Determine whether the user can request a refund.
     */
    public function requestRefund(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only the buyer can request refund, and only if purchase is in escrow
        return $channelPurchase->buyer_id === $user->id && 
               $channelPurchase->isInEscrow();
    }

    /**
     * Determine whether the user can view purchase details.
     */
    public function viewDetails(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Both buyer and seller can view purchase details
        return $channelPurchase->buyer_id === $user->id || 
               $channelPurchase->channelSale->user_id === $user->id;
    }

    /**
     * Determine whether the user can cancel the purchase.
     */
    public function cancel(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only the buyer can cancel, and only if purchase is pending
        return $channelPurchase->buyer_id === $user->id && 
               $channelPurchase->isPending();
    }

    /**
     * Determine whether the user can view the seller's contact information.
     */
    public function viewSellerContact(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only the buyer can view seller contact after purchase is in escrow
        return $channelPurchase->buyer_id === $user->id && 
               ($channelPurchase->isInEscrow() || $channelPurchase->isCompleted());
    }

    /**
     * Determine whether the user can view the buyer's contact information.
     */
    public function viewBuyerContact(User $user, ChannelPurchase $channelPurchase): bool
    {
        // Only the seller can view buyer contact after purchase is in escrow
        return $channelPurchase->channelSale->user_id === $user->id && 
               ($channelPurchase->isInEscrow() || $channelPurchase->isCompleted());
    }
}