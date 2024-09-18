<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/profile', [ProfileController::class, 'loadProfile'])->middleware('auth')->name('profile.load');