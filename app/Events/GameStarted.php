<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Game;

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $status;
    public $gameId;

    public function __construct(Game $game)
    {
        $this->status = $game->status;
        $this->gameId = $game->id;
             \Log::info("PlayerLeftGame EVENT DATA: name=   $this->status | game=   $this->gameId");
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->gameId);
    }
    public function broadcastAs()
{
    return 'game.started';
}

}
