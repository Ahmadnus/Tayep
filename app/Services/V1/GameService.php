<?php

namespace App\Services\V1;

use App\Models\Game;
use App\Models\GamePlayer;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Events\PlayerJoinedGame;
use App\Events\GameStarted;
use App\Traits\GameValidationTrait;
use App\Events\PlayerLeftGame;
use App\Events\NewOwner;

class GameService
{
    use GameValidationTrait;

    public function createGame()
    {
        $existing = Game::whereHas('players', function ($q) {
            $q->where('user_id', Auth::id());
        })->first();

        if ($existing) {
            return [
                'error' => true,
                'message' => trans('game.cannot_create_in_game'),
                'status' => 400
            ];
        }

        $game = Game::create([
            'code' => strtoupper(Str::random(6)),
            'status' => 'waiting',
            'round_count' => 10,
            'current_round' => 0,
            'created_by' => Auth::id(),
        ]);

        $game->players()->create([
            'user_id' => Auth::id(),
            'points' => 0,
        ]);

        return [
            'message' =>trans('game.created_success'),
            'game' => $game,
            'status' => 201
        ];
    }

    public function join(string $code)
    {
        $game = Game::findOrFail(Game::where('code', $code)->value('id'));

        $checkStatus = $this->ensureGameIsWaiting($game);
        if (is_array($checkStatus)) return $checkStatus;

        if ($game->players()->count() >= 5) {
            return [
                'error' => true,
                'message' => __('game.full'),
                'status' => 403
            ];
        }

        $existingPlayer = $this->ensurePlayerInGame($game);
        if (!is_array($existingPlayer)) {
            return [
                'message' => __('game.already_joined'),
                'game' => $game,
                'status' => 200
            ];
        }

        $player = $game->players()->create([
            'user_id' => Auth::id(),
            'points' => 0,
        ]);

        event(new PlayerJoinedGame($player));

        return [
            'message' => __('game.joined_success'),
            'game' => $game,
            'status' => 200
        ];
    }

    public function leave(int $gameId)
    {
        $game = $this->findGameOrFail($gameId);

        $playerCheck = $this->ensurePlayerInGame($game);
        if (is_array($playerCheck)) return $playerCheck;
        $player = $playerCheck;

        if ($game->created_by == Auth::id()) {
            $player->delete();
            $newOwner = $game->players()->orderBy('created_at', 'asc')->first();

            if (!$newOwner) {
                $game->delete();
                return [
                    'message' => __('game.owner_left_room_deleted'),
                    'status' => 200
                ];
            }

            $game->update(['created_by' => $newOwner->user_id]);

            event(new NewOwner($game));
            return [
                'message' => __('game.owner_left_transfer'),
                'new_owner_id' => $newOwner->user_id,
                'status' => 200
            ];
        }

        $player->delete();

        if ($game->players()->count() === 0) {
            $game->delete();
            return [
                'message' => __('game.owner_left_room_deleted'),
                'status' => 200
            ];
        }

        event(new PlayerLeftGame($player->user->name, $game->id));

        return [
            'message' => __('game.left_success'),
            'status' => 200
        ];
    }

    public function generateQr(string $code)
    {
        $game = Game::where('code', $code)->firstOrFail();
        $joinUrl = url('/join/' . $game->code);

        return Builder::create()
            ->data($joinUrl)
            ->size(300)
            ->margin(10)
            ->build();
    }

    public function kick(int $gameId, int $userId)
    {
        $game = $this->findGameOrFail($gameId);

        $ownerCheck = $this->ensureOwner($game);
        if (is_array($ownerCheck)) return $ownerCheck;

        if ($userId == $game->created_by) {
            return [
                'error' => true,
                'message' => __('game.cannot_kick_self'),
                'status' => 400
            ];
        }

        $player = $game->players()->where('user_id', $userId)->first();
        if (!$player) {
            return [
                'error' => true,
                'message' => __('game.player_not_found'),
                'status' => 404
            ];
        }

        if ($game->status === 'playing') {
            return [
                'error' => true,
                'message' => __('game.cannot_kick_during_play'),
                'status' => 403
            ];
        }

        $player->delete();

        if ($game->players()->count() === 0) {
            $game->delete();
            return [
                'message' => __('game.kick_room_empty'),
                'status' => 200
            ];
        }

        return [
            'message' => __('game.kicked_success'),
            'status' => 200
        ];
    }

   public function start(int $id)
{
    $game = $this->findGameOrFail($id);

    $playerCheck = $this->ensurePlayerInGame($game);
    if (is_array($playerCheck)) return $playerCheck;

    $ownerCheck = $this->ensureOwner($game);
    if (is_array($ownerCheck)) return $ownerCheck;

    if ($game->status === 'playing') {
        return [
            'error' => true,
            'message' => __('game.already_playing'),
            'status' => 400
        ];
    }

    if ($game->status === 'finished') {
        return [
            'error' => true,
            'message' => __('game.already_finished'),
            'status' => 400
        ];
    }

    $playerCountCheck = $this->ensurePlayerCount($game, 2);
    if (is_array($playerCountCheck)) return $playerCountCheck;

    // بدء اللعبة وأول جولة
    $game->update([
        'status' => 'playing',
        'current_round' => 1,
    ]);

    event(new GameStarted($game));

    return [
        'message' => __('game.start_success'),
        'status' => 200
    ];
}

    public function getGameDetails(int $gameId)
    {
        $game = $this->findGameOrFail($gameId);

        $playerCheck = $this->ensurePlayerInGame($game);
        if (is_array($playerCheck)) return $playerCheck;

        $game->load(['players.user']);

        return [
            'message' => __('game.game_details_success'),
            'game' => $game,
            'status' => 200
        ];
    }
}
