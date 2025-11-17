<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('player_answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('round_id')->constrained('rounds')->onDelete('cascade');
        $table->foreignId('player_id')->constrained('game_players')->onDelete('cascade');
        $table->text('answer_text');
        $table->boolean('is_correct')->nullable();
        $table->integer('votes_received')->default(0);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_answers');
    }
};
