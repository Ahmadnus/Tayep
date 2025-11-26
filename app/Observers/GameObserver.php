<?php

namespace App\Observers;

use App\Models\Game;





use App\Events\RoundStarted;

class GameObserver
{
    /**
     * Handle the Game "created" event.
     */
    public function created(Game $game): void
    {
        //
    }

    /**
     * Handle the Game "updated" event.
     */



    public function updating(Game $game)
    {
        // تحقق إذا كان current_round رح يتغير
        if ($game->isDirty('current_round')) {

            // مثال: إذا زاد رقم الجولة
            $old = $game->getOriginal('current_round');
            $new = $game->current_round;

           
        }
    
}


    /**
     * Handle the Game "deleted" event.
     */
    public function deleted(Game $game): void
    {
        //
    }

    /**
     * Handle the Game "restored" event.
     */
    public function restored(Game $game): void
    {
        //
    }

    /**
     * Handle the Game "force deleted" event.
     */
    public function forceDeleted(Game $game): void
    {
        //
    }
}
