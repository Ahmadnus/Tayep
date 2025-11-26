<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Http\Request;
use App\Events\RoundStarted;
use App\Events\RoundEnded;
use App\Services\V1\RoundService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\V1\NextRoundRequest ;
use App\Http\Requests\V1\GetCurrentRoundRequest ;
use App\Http\Resources\V1\GetCurrentRoundResource;
use App\Http\Resources\V1\GameRoundResource;
use App\Http\Resources\V1\NextRoundResource;



class RoundController extends Controller
{
       use ApiResponseTrait;
  protected $service;

    public function __construct(RoundService $service)
    {
        $this->service = $service;
    }


    /**
     * انتقل للجولة التالية
     */
public function nextRound(NextRoundRequest $request)
{
    $result = $this->service->nextRound($request->gameId);

    if (isset($result['error'])) {
        return $this->errorResponse($result['message'], $result['status']);
    }

    return $this->successResponse(
        new NextRoundResource((object) $result),
        $result['message']  // أو __('round.next_round_success')
    );
}

public function getCurrentRound(GetCurrentRoundRequest $request)
{
    $result = $this->service->getCurrentRound($request->gameId);

    return $this->successResponse(
        new GameRoundResource((object) $result),
        $result['message']  // أو __('round.current_round_success')
    );
}

public function endRound(EndRoundRequest $request)
{
    $result = $this->service->endRound($request->game_id);

    if (isset($result['error'])) {
        return $this->errorResponse($result['message'], $result['status']);
    }

    return $this->successResponse([
        'current_round' => $result['current_round']
    ], $result['message'], $result['status']);
}
}
