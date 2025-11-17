<?php

namespace App\Services\V1;

use App\Models\Game;
use App\Models\GamePlayer;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Traits\GameValidationTrait;

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
                'message' => 'لا يمكنك إنشاء غرفة جديدة لأنك داخل لعبة حالياً.',
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
            'message' => 'تم إنشاء الغرفة بنجاح.',
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
                'message' => 'الغرفة ممتلئة، لا يمكنك الانضمام.',
                'status' => 403
            ];
        }

        $existingPlayer = $this->ensurePlayerInGame($game);
        if (!is_array($existingPlayer)) {
            return [
                'message' => 'أنت موجود مسبقاً داخل اللعبة.',
                'game' => $game,
                'status' => 200
            ];
        }

        $game->players()->create([
            'user_id' => Auth::id(),
            'points' => 0,
        ]);

        return [
            'message' => 'تم الانضمام إلى اللعبة بنجاح.',
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
                    'message' => 'تم حذف الغرفة لأنها أصبحت فارغة.',
                    'status' => 200
                ];
            }

            $game->update(['created_by' => $newOwner->user_id]);

            return [
                'message' => 'تم نقل ملكية الغرفة للاعب التالي ومغادرة الغرفة.',
                'new_owner_id' => $newOwner->user_id,
                'status' => 200
            ];
        }

        $player->delete();

        if ($game->players()->count() === 0) {
            $game->delete();
            return [
                'message' => 'تم حذف الغرفة لأنها أصبحت فارغة.',
                'status' => 200
            ];
        }

        return [
            'message' => 'تمت مغادرة اللعبة بنجاح.',
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
                'message' => 'لا يمكنك طرد نفسك لأنك صاحب الغرفة.',
                'status' => 400
            ];
        }

        $player = $game->players()->where('user_id', $userId)->first();
        if (!$player) {
            return [
                'error' => true,
                'message' => 'هذا اللاعب غير موجود داخل هذه الغرفة.',
                'status' => 404
            ];
        }

        if ($game->status === 'playing') {
            return [
                'error' => true,
                'message' => 'لا يمكن طرد اللاعبين بعد بدء اللعبة.',
                'status' => 403
            ];
        }

        $player->delete();

        if ($game->players()->count() === 0) {
            $game->delete();
            return [
                'message' => 'تم حذف اللاعب، الغرفة أصبحت فارغة.',
                'status' => 200
            ];
        }

        return [
            'message' => 'تم طرد اللاعب بنجاح.',
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
                'message' => 'اللعبة بدأت بالفعل.',
                'status' => 400
            ];
        }

        if ($game->status === 'finished') {
            return [
                'error' => true,
                'message' => 'لا يمكن بدء لعبة منتهية.',
                'status' => 400
            ];
        }

        $playerCountCheck = $this->ensurePlayerCount($game, 2);
        if (is_array($playerCountCheck)) return $playerCountCheck;

        $game->update([
            'status' => 'playing',
            'current_round' => 1,
        ]);

        return [
            'message' => 'تم بدء اللعبة بنجاح.',
            'status' => 200
        ];
    }

    public function getGameDetails(int $gameId)
{
    $game = $this->findGameOrFail($gameId);

    $playerCheck = $this->ensurePlayerInGame($game);
    if (is_array($playerCheck)) {
        return $playerCheck; // اللاعب ليس داخل اللعبة
    }

    // تحميل اللاعبين والعلاقة بصاحب اللعبة
    $game->load(['players.user']);

    return [
        'message' => 'تم جلب تفاصيل اللعبة بنجاح.',
        'game' => $game,
        'status' => 200
    ];
}

}
