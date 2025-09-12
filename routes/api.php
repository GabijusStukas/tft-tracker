<?php

use App\Http\Controllers\SummonerController;

Route::middleware('auth')->group(function () {
    //
});

Route::middleware('guest')->group(function () {
    Route::get('summoner/search', [SummonerController::class, 'show'])->name('summoner.search');
});
