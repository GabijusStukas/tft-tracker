<?php

use App\Http\Middleware\AuthenticateWithJwt;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(AuthenticateWithJwt::class)->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('{game}/summoners/{region}/{username}-{tagLine}', function ($game, $region, $username, $tagLine) {
        return Inertia::render('Summoner', [
            'game' => $game,
            'region' => $region,
            'username' => $username,
            'tagLine' => $tagLine
        ]);
    })->name('summoner');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
