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
        Schema::create('riot_match_participants', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('match_id');
            $table->string('puuid');
            $table->string('game_name');
            $table->string('tag_line');
            $table->integer('level');
            $table->integer('gold_left');
            $table->integer('placement');
            $table->integer('last_round');

            $table->foreign('match_id')
                ->references('id')
                ->on('riot_matches')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riot_match_participants');
    }
};
