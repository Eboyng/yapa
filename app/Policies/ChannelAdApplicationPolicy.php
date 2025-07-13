<?php

namespace App\Policies;

use App\Models\ChannelAdApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelAdApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->advertiser_id || 
               $user->id === $application->channelAd->owner_id;
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
    public function update(User $user, ChannelAdApplication $application): bool
    {
        // Only allow updates if the application is still pending
        return $application->booking_status === 'pending' && 
               ($user->id === $application->advertiser_id || 
                $user->id === $application->channelAd->owner_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->advertiser_id && 
               $application->booking_status === 'pending';
    }

    /**
     * Determine whether the user can approve the application.
     */
    public function approve(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->channelAd->owner_id && 
               $application->booking_status === 'pending' && 
               $application->payment_status === 'held';
    }

    /**
     * Determine whether the user can reject the application.
     */
    public function reject(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->channelAd->owner_id && 
               $application->booking_status === 'pending';
    }

    /**
     * Determine whether the user can cancel the application.
     */
    public function cancel(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->advertiser_id && 
               in_array($application->booking_status, ['pending', 'confirmed']) && 
               $application->start_date->isFuture() && 
               in_array($application->payment_status, ['pending', 'held']);
    }

    /**
     * Determine whether the user can request a refund.
     */
    public function refund(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->advertiser_id && 
               in_array($application->booking_status, ['confirmed', 'completed']) && 
               $application->payment_status === 'released';
    }

    /**
     * Determine whether the user can complete the application.
     */
    public function complete(User $user, ChannelAdApplication $application): bool
    {
        return $user->id === $application->channelAd->owner_id && 
               $application->booking_status === 'confirmed' && 
               $application->payment_status === 'released' && 
               $application->end_date->isPast();
    }
}