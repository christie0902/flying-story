<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/calendar');


// Profile routes
Route::get('/profile', [ProfileController::class, 'loadProfile'])->middleware('auth')->name('profile.load');
Route::get('/profile/edit', [ProfileController::class, 'editForm'])->middleware('auth')->name('profile.editForm');
Route::put('/profile/edit', [ProfileController::class, 'updateProfile'])->middleware('auth')->name('profile.update');
Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.changeForm');


//Calendar route
Route::get('/calendar/load-events', [CalendarController::class, 'loadLessons'])->name('calendar.load');
Route::get('/calendar', [CalendarController::class, 'showCalendar'])->name('calendar.show');
Route::get('/calendar/lesson/{id}', [CalendarController::class, 'getLessonDetails'])->name('calendar.lesson');