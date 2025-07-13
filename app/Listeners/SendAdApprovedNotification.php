<?php

namespace App\Listeners;

use App\Events\AdApproved;
use App\Notifications\AdApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdApprovedNotification implements ShouldQueue
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
    public function handle(AdApproved $event): void
    {
        // Notify the advertiser about the approval
        $advertiser = $event->application->advertiser;
        
        if ($advertiser) {
            $advertiser->notify(new AdApprovedNotification($event->application));
        }
    }
}