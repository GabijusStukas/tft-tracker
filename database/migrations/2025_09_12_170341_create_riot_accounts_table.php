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
        Schema::create('riot_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('puuid')->unique();
            $table->string('game_name');
            $table->string('tag_line');
            $table->string('region');
            $table->string('game');
            $table->timestamps();

            $table->foreign('region')
                ->references('region')
                ->on('riot_regions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riot_accounts');
    }
};
