<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/


Route::group(['middleware' => 'can:admin'], function () {
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
});
