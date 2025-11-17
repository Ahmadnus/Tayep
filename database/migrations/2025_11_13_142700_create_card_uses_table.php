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
    Schema::create('card_uses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
        $table->foreignId('round_id')->constrained('rounds')->onDelete('cascade');
        $table->foreignId('card_id')->constrained('game_cards')->onDelete('cascade');
        $table->foreignId('used_by')->constrained('users')->onDelete('cascade');
        $table->foreignId('target_id')->nullable()->constrained('users')->onDelete('set null');
        $table->string('effect_result')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_uses');
    }
};
