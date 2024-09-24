<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});


// Profile routes
Route::get('/profile', [ProfileController::class, 'loadProfile'])->middleware('auth')->name('profile.load');
Route::get('/profile/edit', [ProfileController::class, 'editForm'])->middleware('auth')->name('profile.editForm');
Route::put('/profile/edit', [ProfileController::class, 'updateProfile'])->middleware('auth')->name('profile.update');
Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.changeForm');

//Lesson routes
Route::get('/lessons', [LessonController::class, 'loadLessons'])->name('lesson.list');

Route::get('/lessons/add', [LessonController::class, 'createLesson'])->name('lessons.create');
Route::post('/lessons/add', [LessonController::class, 'storeLesson'])->name('lessons.store');

Route::get('/lessons/edit/{id}', [LessonController::class, 'editLesson'])->name('lessons.edit');
Route::put('/lessons/edit/{id}', [LessonController::class, 'updateLesson'])->name('lessons.update');
Route::delete('/lessons/edit/{id}', [LessonController::class, 'deleteLesson'])->name('lessons.delete');
Route::get('/lessons/show/{id}', [LessonController::class, 'details'])->name('lessons.details');

Route::put('/lessons/edit/{id}/cancel', [LessonController::class, 'cancel'])->name('lessons.cancel');
Route::put('/lessons/edit/{id}/activate', [LessonController::class, 'activate'])->name('lessons.activate');