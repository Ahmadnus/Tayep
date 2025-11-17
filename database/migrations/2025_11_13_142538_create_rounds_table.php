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
    Schema::create('rounds', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
        $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
        $table->integer('round_number');
        $table->enum('status', ['pending', 'active', 'finished'])->default('pending');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
