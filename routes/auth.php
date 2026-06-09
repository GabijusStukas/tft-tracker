<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\JwtLogoutController;
use App\Http\Controllers\Auth\JwtRefreshController;
use App\Http\Middleware\AuthenticateWithJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware(AuthenticateWithJwt::class)->group(function () {
    Route::post('auth/logout', [JwtLogoutController::class, 'logout'])->name('auth.logout');
    Route::post('auth/refresh', [JwtRefreshController::class, 'refresh'])->name('auth.refresh');

    Route::get('auth/user', function (Request $request) {
        return $request->user();
    })->name('auth.user');
});
