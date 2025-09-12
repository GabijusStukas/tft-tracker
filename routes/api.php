<?php

use App\Http\Controllers\FoodController;

Route::middleware('auth')->group(function () {
    Route::post('new-meal', [FoodController::class, 'store'])->name('submit-meal');
    Route::get('food/{food}/image', [FoodController::class, 'show'])->name('food.image');
});
