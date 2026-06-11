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
        Schema::create('riot_matches', function (Blueprint $table) {
            $table->id();

            $table->string('match_id')->index();
            $table->string('game_version')->index();
            $table->string('queue_name')->index();
            $table->integer('season')->index();
            $table->json('raw_data');
            $table->timestamp('match_created_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riot_matches');
    }
};
