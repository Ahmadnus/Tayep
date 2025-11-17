<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateGameRequest;
use App\Http\Requests\V1\JoinGameRequest;
use App\Http\Requests\V1\LeaveGameRequest;
use App\Http\Requests\V1\KickPlayerRequest;
use App\Http\Resources\V1\GameResource;
use App\Services\V1\GameService;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;
use App\Traits\ApiResponseTrait;

class GameController extends Controller
{
    use ApiResponseTrait;

    protected $service;

    public function __construct(GameService $service)
    {
        $this->service = $service;
    }

    public function createGame(CreateGameRequest $request)
    {
        $game = $this->service->createGame();
        if (isset($game['error'])) {
            return $this->errorResponse($game['message'], $game['status']);
        }

        return $this->successResponse(['game' => new GameResource($game['game'])], $game['message'], $game['status']);
    }

    public function joinGame(JoinGameRequest $request)
    {
        $result = $this->service->join($request->code);

        if (isset($result['error'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(['game' => new GameResource($result['game'])], $result['message'], $result['status']);
    }

    public function leaveGame(LeaveGameRequest $request)
    {
        $result = $this->service->leave($request->game_id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse([], $result['message'], $result['status']);
    }

    public function generateGameQr($code)
    {
        $qr = $this->service->generateQr($code);
        return response($qr->getString())->header('Content-Type', 'image/png');
    }

    public function kickPlayer(KickPlayerRequest $request, $gameId)
    {
        $result = $this->service->kick($gameId, $request->user_id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse([], $result['message']);
    }

    public function startGame($id)
    {
        $result = $this->service->start($id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse([], $result['message']);
    }

    public function getGameDetails($gameId)
    {
        $game = Game::with(['players.user', 'owner'])->find($gameId);
        if (!$game) {
            return $this->errorResponse('اللعبة غير موجودة', 404);
        }

        return $this->successResponse(['game' => new GameResource($game)], 'تم جلب تفاصيل اللعبة بنجاح.');
    }

    public function myCurrentGame()
    {
        $game = Game::whereHas('players', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['players.user', 'owner'])
            ->first();

        if (!$game) {
            return $this->successResponse(['in_game' => false], 'أنت حالياً لست داخل أي لعبة.');
        }

        return $this->successResponse(['in_game' => true, 'game' => new GameResource($game)], 'تم جلب اللعبة الحالية.');
    }
}
