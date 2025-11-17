<?php

namespace App\Models;
use App\Http\Controllers\Api\V1\GameController;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'code',
        'status',
        'round_count',
        'current_round',
        'created_by',
    ];
public function owner()
{
    return $this->belongsTo(User::class, 'created_by');
}
public function players()
{
    return $this->hasMany(GamePlayer::class);
}
}
