<?php

namespace App\Policies;

use App\Models\ChannelAd;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelAdPolicy
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
    public function view(User $user, ChannelAd $channelAd): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChannelAd $channelAd): bool
    {
        return $user->id === $channelAd->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChannelAd $channelAd): bool
    {
        return $user->id === $channelAd->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChannelAd $channelAd): bool
    {
        return $user->id === $channelAd->owner_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChannelAd $channelAd): bool
    {
        return $user->id === $channelAd->owner_id;
    }

    /**
     * Determine whether the user can book ads on this channel.
     */
    public function bookAd(User $user, ChannelAd $channelAd): bool
    {
        return $user->hasVerifiedEmail() && 
               $user->id !== $channelAd->owner_id && 
               $channelAd->status === 'active';
    }

    /**
     * Determine whether the user can manage bookings for this channel.
     */
    public function manageBookings(User $user, ChannelAd $channelAd): bool
    {
        return $user->id === $channelAd->owner_id;
    }
}