<?php

namespace App\Listeners;

use App\Events\AdCompleted;
use App\Notifications\AdCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdCompletedNotification implements ShouldQueue
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
    public function handle(AdCompleted $event): void
    {
        // Notify the advertiser about the completion
        $advertiser = $event->application->advertiser;
        
        if ($advertiser) {
            $advertiser->notify(new AdCompletedNotification($event->application));
        }
    }
}