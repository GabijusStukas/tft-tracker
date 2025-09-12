<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('new-meal', function () {
        return Inertia::render('NewMeal');
    })->name('new-meal');

    Route::post('new-meal', function () {
        // Logic to handle new meal submission
        return redirect()->route('dashboard')->with('success', 'Meal created successfully!');
    })->name('submit-meal');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/api.php';
