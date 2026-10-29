<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RouteLockPermission implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $status;
    public $routeId;

    /**
     * Create a new event instance.
     */
    public function __construct($status, $routeId)
    {
        $this->status = $status;
        $this->routeId = $routeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // return [
        //     new PrivateChannel('websockets-dashboard-api-message'),
        // ];

         return [
            new PrivateChannel('lockpermission'),
        ];
    }

        //     public function broadcastAs()
        //   {
        //       return 'send-location';
        //   }

        // public function broadcastWith()
        // {
        //     return $this->routeId;
        // }
}
