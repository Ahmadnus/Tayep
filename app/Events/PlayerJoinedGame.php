<?php

namespace App\Events;

use App\Models\GamePlayer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class PlayerJoinedGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;




    public $playerName;
    public $gameId;

    public function __construct(GamePlayer $player)
    {
        $this->playerName = $player->user->name;
        $this->gameId = $player->game_id;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->gameId);
    }

}
