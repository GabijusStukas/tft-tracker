<?php

use App\Http\Controllers\Auth\JwtLogoutController;
use App\Http\Controllers\Auth\JwtRefreshController;
use App\Http\Controllers\SummonerController;

Route::middleware(['auth:api'])->group(function () {
    Route::post('auth/logout', [JwtLogoutController::class, 'logout'])->name('auth.logout');
    Route::post('auth/refresh', [JwtRefreshController::class, 'refresh'])->name('auth.refresh');

    Route::get('summoners/search', [SummonerController::class, 'search'])->name('summoner.search');
    Route::post('summoners/{username}-{tag_line}', [SummonerController::class, 'show'])->name('summoner.show');
});
