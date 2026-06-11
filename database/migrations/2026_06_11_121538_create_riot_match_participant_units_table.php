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
        Schema::create('riot_match_participant_units', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('participant_id');
            $table->string('character_id')->index();
            $table->string('name')->nullable();
            $table->integer('tier');
            $table->integer('rarity');
            $table->string('icon')->nullable();
            $table->json('items');

            $table->foreign('participant_id')
                ->references('id')
                ->on('riot_match_participants')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riot_match_participant_units');
    }
};
