<?php

namespace App\Services\V1;
use App\Models\Game;
use App\Events\RoundEnded;
use App\Events\RoundStarted;
class RoundService
{
    
  
   public function nextRound($gameId)
    {
        try {
            return DB::transaction(function () use ($gameId) {

                $game = Game::findOrFail($gameId);

                if ($game->status !== 'playing') {
                    return [
                        'error'   => true,
                        'message' => trans('messages.game_not_playing'),
                        'status'  => 400
                    ];
                }

                // زيادة رقم الجولة
                $game->current_round += 1;
                $game->save();

                // بث حدث بدء الجولة
                event(new RoundStarted($game, $game->current_round));

                return [
                    'message'       => trans('messages.next_round_success'),
                    'current_round' => $game->current_round,
                    'status'        => 200
                ];
            });

        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => trans('messages.internal_error'),
                'status'  => 500
            ];
        }
    }

    /**
     * الحصول على الجولة الحالية
     */
    public function getCurrentRound($gameId)
    {
        $game = Game::findOrFail($gameId);

        return [
            'current_round' => $game->current_round,
            'status'        => $game->status,
            'message'       => trans('rounds.current_round_success')
        ];
    }

    /**
     * إنهاء الجولة الحالية
     */
    public function endRound(int $gameId)
    {
        try {
            return DB::transaction(function () use ($gameId) {

                $game = Game::findOrFail($gameId);

                if ($game->current_round === 0) {
                    return [
                        'error'   => true,
                        'message' => trans('messages.no_active_round'),
                        'status'  => 400
                    ];
                }

                $currentRound = $game->current_round;

                // بث حدث انتهاء الجولة
                event(new RoundEnded($game, $currentRound));

                return [
                    'message'       => trans('messages.round_ended_success'),
                    'current_round' => $currentRound,
                    'status'        => 200
                ];
            });

        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => trans('messages.internal_error'),
                'status'  => 500
            ];
        }
    }

}


