<?php

use App\Http\Controllers\Auth\JwtLogoutController;
use App\Http\Controllers\Auth\JwtRefreshController;
use App\Http\Controllers\SummonerController;
use App\Http\Controllers\SummonerMatchesController;
use App\Http\Middleware\AuthenticateWithJwt;

Route::middleware(AuthenticateWithJwt::class)->group(function () {
    Route::post('auth/logout', [JwtLogoutController::class, 'logout'])->name('auth.logout');
    Route::post('auth/refresh', [JwtRefreshController::class, 'refresh'])->name('auth.refresh');

    Route::get('summoners/search', [SummonerController::class, 'search'])->name('summoner.search');

    Route::prefix('{game}/summoners/{region}/{username}-{tag_line}')->group(function () {
        Route::get('profile', [SummonerController::class, 'index'])->name('summoner.matches.index');
        Route::get('matches', [SummonerMatchesController::class, 'index'])->name('summoner.matches.index');
    });
});
