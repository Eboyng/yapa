<?php

namespace App\Policies;

use App\Models\ChannelSale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelSalePolicy
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
    public function view(User $user, ChannelSale $channelSale): bool
    {
        // Users can view their own listings or publicly available listings
        return $channelSale->user_id === $user->id || 
               ($channelSale->visibility && $channelSale->isListed());
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
    public function update(User $user, ChannelSale $channelSale): bool
    {
        // Only the owner can update their listing, and only if it's not sold
        return $channelSale->user_id === $user->id && !$channelSale->isSold();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChannelSale $channelSale): bool
    {
        // Only the owner can delete their listing, and only if it's not sold
        return $channelSale->user_id === $user->id && !$channelSale->isSold();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChannelSale $channelSale): bool
    {
        return $channelSale->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChannelSale $channelSale): bool
    {
        return $channelSale->user_id === $user->id;
    }

    /**
     * Determine whether the user can edit the listing.
     */
    public function edit(User $user, ChannelSale $channelSale): bool
    {
        return $this->update($user, $channelSale);
    }

    /**
     * Determine whether the user can remove the listing.
     */
    public function remove(User $user, ChannelSale $channelSale): bool
    {
        // Only the owner can remove their listing, and only if it's not sold
        return $channelSale->user_id === $user->id && !$channelSale->isSold();
    }

    /**
     * Determine whether the user can toggle visibility of the listing.
     */
    public function toggleVisibility(User $user, ChannelSale $channelSale): bool
    {
        // Only the owner can toggle visibility, and only if it's listed
        return $channelSale->user_id === $user->id && $channelSale->isListed();
    }

    /**
     * Determine whether the user can relist a removed channel.
     */
    public function relist(User $user, ChannelSale $channelSale): bool
    {
        // Only the owner can relist their removed channel
        return $channelSale->user_id === $user->id && $channelSale->isRemoved();
    }

    /**
     * Determine whether the user can purchase this channel.
     */
    public function purchase(User $user, ChannelSale $channelSale): bool
    {
        // Users cannot buy their own channels, and channel must be available
        return $channelSale->user_id !== $user->id && 
               $channelSale->isListed() && 
               $channelSale->visibility;
    }
}