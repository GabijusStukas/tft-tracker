<?php

use App\Http\Controllers\Auth\JwtLogoutController;
use App\Http\Controllers\Auth\JwtRefreshController;
use App\Http\Controllers\RiotAccountController;
use App\Http\Controllers\RiotLeagueController;
use App\Http\Controllers\RiotMatchesController;
use App\Http\Middleware\AuthenticateWithJwt;

Route::middleware(AuthenticateWithJwt::class)->group(function () {
    Route::post('auth/logout', [JwtLogoutController::class, 'logout'])->name('auth.logout');
    Route::post('auth/refresh', [JwtRefreshController::class, 'refresh'])->name('auth.refresh');

    Route::get('summoners/search', [RiotAccountController::class, 'search'])->name('riot.account.search');

    Route::get('tft/match/{match_id}', [RiotMatchesController::class, 'show'])->name('riot.match.show');

    Route::prefix('{game}/summoners/{region}/{username}-{tag_line}')->group(function () {
        Route::get('profile', [RiotAccountController::class, 'index'])->name('riot.account.index');
        Route::get('matches', [RiotMatchesController::class, 'index'])->name('riot.matches');
        Route::get('league', [RiotLeagueController::class, 'index'])->name('riot.league');
    });
});
