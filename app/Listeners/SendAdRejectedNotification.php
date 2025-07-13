<?php

namespace App\Listeners;

use App\Events\AdRejected;
use App\Notifications\AdRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdRejectedNotification implements ShouldQueue
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
    public function handle(AdRejected $event): void
    {
        // Notify the advertiser about the rejection
        $advertiser = $event->application->advertiser;
        
        if ($advertiser) {
            $advertiser->notify(new AdRejectedNotification($event->application, $event->reason));
        }
    }
}