<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'user_id'=> $this->user_id,
            'name'   => $this->user->name ?? null,
            'avatar' => $this->user->avatar ?? null,
            'points' => $this->points,
        ];
    }
}
