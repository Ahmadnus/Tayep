<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLeftGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
   public $playerName;
    public $gameId;

    public function __construct($playerName, $gameId)
    {
        $this->playerName = $playerName;
        $this->gameId = $gameId;
         \Log::info("PlayerLeftGame EVENT DATA: name=$playerName | game=$gameId");
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->gameId);
    }
}

