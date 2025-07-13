<?php

namespace App\Listeners;

use App\Events\AdRefunded;
use App\Notifications\AdRefundedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdRefundedNotification implements ShouldQueue
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
    public function handle(AdRefunded $event): void
    {
        // Notify the advertiser about the refund
        $advertiser = $event->application->advertiser;
        
        if ($advertiser) {
            $advertiser->notify(new AdRefundedNotification($event->application, $event->reason));
        }
    }
}