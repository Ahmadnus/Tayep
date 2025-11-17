<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Api\V1\GameController;
class GamePlayer extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'points',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
