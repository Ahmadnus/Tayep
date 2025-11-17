<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'code'          => $this->code,
            'status'        => $this->status,
            'round_count'   => $this->round_count,
            'current_round' => $this->current_round,
            'created_by'    => $this->created_by,
            'owner'         => $this->whenLoaded('owner', function () {
                return [
                    'id'   => $this->owner->id,
                    'name' => $this->owner->name ?? null,
                ];
            }),
            'players'       => $this->whenLoaded('players', function () {
                return $this->players->map(function ($player) {
                    return [
                        'id'     => $player->user->id ?? null,
                        'name'   => $player->user->name ?? null,
                        'points' => $player->points,
                    ];
                });
            }),
            'created_at'    => $this->created_at->toDateTimeString(),
            'updated_at'    => $this->updated_at->toDateTimeString(),
        ];
    }
}
