<?php

namespace App\Events;

use App\Models\ChannelAdApplication;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdRefunded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChannelAdApplication $application;
    public ?string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ChannelAdApplication $application, ?string $reason = null)
    {
        $this->application = $application;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel'),
        ];
    }
}