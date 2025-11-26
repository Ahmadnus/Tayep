<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoundStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameId;
    public $roundNumber;

    public function __construct(Game $game, int $roundNumber)
    {
        $this->gameId = $game->id;
        $this->roundNumber = $roundNumber;

        \Log::info("RoundStarted EVENT: game={$this->gameId}, round={$this->roundNumber}");
    }

    public function broadcastOn(): Channel
    {
        return new Channel('game.' . $this->gameId);
    }

    public function broadcastAs(): string
    {
        return 'round.started';
    }
}
