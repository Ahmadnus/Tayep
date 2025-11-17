<?php

namespace App\Traits;
use App\Models\Game;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\GamePlayer;
trait GameValidationTrait
{
    protected function findGameOrFail($id)
    {
        return Game::findOrFail($id);
    }

    // التأكد أن المستخدم داخل اللعبة
    protected function ensurePlayerInGame($game)
    {
        $player = $game->players()
            ->where('user_id', Auth::id())
            ->first();

        if (!$player) {
            return [
                'error' => true,
                'message' => 'أنت لست داخل هذه اللعبة.',
                'status' => 404
            ];
        }

        return $player;
    }

    // التأكد أن اللعبة في حالة انتظار (waiting)
    protected function ensureGameIsWaiting($game)
    {
        if ($game->status !== 'waiting') {
            return [
                'error' => true,
                'message' => 'لا يمكنك الانضمام لأن اللعبة قد بدأت.',
                'status' => 403
            ];
        }

        return true;
    }

    // التأكد أن المستخدم هو صاحب الغرفة
    protected function ensureOwner($game)
    {
        if ($game->created_by !== Auth::id()) {
            return [
                'error' => true,
                'message' => 'فقط صاحب الغرفة يمكنه تنفيذ هذا الإجراء.',
                'status' => 403
            ];
        }

        return true;
    }

    // التأكد من عدد اللاعبين
    protected function ensurePlayerCount(Game $game, int $minPlayers)
    {
        $count = $game->players()->count();
        if ($count < $minPlayers) {
            return [
                'error' => true,
                'message' => "لا يمكنك تنفيذ هذا الإجراء لأن عدد اللاعبين أقل من {$minPlayers}.",
                'status' => 400
            ];
        }

        return true;
    }

}