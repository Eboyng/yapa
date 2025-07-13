<?php

namespace App\Listeners;

use App\Events\AdBooked;
use App\Notifications\AdBookedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdBookedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AdBooked $event): void
    {
        // Notify the channel owner about the new booking
        $channelOwner = $event->application->channelAd->owner;
        
        if ($channelOwner) {
            $channelOwner->notify(new AdBookedNotification($event->application));
        }
    }
}