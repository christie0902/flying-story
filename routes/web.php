<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/profile', [ProfileController::class, 'loadProfile'])->middleware('auth')->name('profile.load');
Route::get('/profile/edit', [ProfileController::class, 'editForm'])->middleware('auth')->name('profile.editForm');
Route::put('/profile/edit', [ProfileController::class, 'updateProfile'])->middleware('auth')->name('profile.update');