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
        Schema::create('riot_match_participant_traits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('participant_id');
            $table->string('trait_id')->index();
            $table->string('name');
            $table->integer('style');
            $table->integer('num_units');
            $table->integer('tier_total');
            $table->integer('tier_current');
            $table->string('icon');

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
        Schema::dropIfExists('riot_match_participant_traits');
    }
};
