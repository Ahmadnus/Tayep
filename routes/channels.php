<?php

use Illuminate\Support\Facades\Broadcast;
use App\Events\PlayerJoinedGame;
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('game.{gameId}', function ($user, $gameId) {
    return $user->id && \App\Models\GamePlayer::where('game_id', $gameId)
                                              ->where('user_id', $user->id)
                                              ->exists();
});
