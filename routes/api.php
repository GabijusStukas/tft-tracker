<?php

use App\Http\Controllers\SummonerController;

Route::middleware('auth')->group(function () {
    //
});

Route::middleware('guest')->group(function () {
    Route::get('summoners/search',                 [SummonerController::class, 'search'])->name('summoner.search');
    Route::post('summoners/{username}-{tag_line}', [SummonerController::class, 'show'])  ->name('summoner.show');
});

