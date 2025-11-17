<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamePlayer;
use App\Models\User;

class GamePlayersSeeder extends Seeder
{
    public function run()
    {
        $gameId = 2; // الغرفة رقم 2

        // خذ أول 5 يوزرات مثلاً
        $users = User::take(5)->get();

        foreach ($users as $user) {
            GamePlayer::create([
                'game_id' => $gameId,
                'user_id' => $user->id,
                'points' => 0,
            ]);
        }
    }
}
