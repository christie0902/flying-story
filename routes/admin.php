<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PaymentInfoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'can:admin'])->group(function () {
    //Lesson routes
    Route::get('/lessons', [LessonController::class, 'loadLessons'])->name('lesson.list');

    Route::get('/lessons/add', [LessonController::class, 'createLesson'])->name('lessons.create');
    Route::post('/lessons/add', [LessonController::class, 'storeLesson'])->name('lessons.store');

    Route::get('/lessons/edit/{id}', [LessonController::class, 'editLesson'])->name('lessons.edit');
    Route::put('/lessons/edit/{id}', [LessonController::class, 'updateLesson'])->name('lessons.update');

    Route::delete('/lessons/edit/{id}', [LessonController::class, 'deleteLesson'])->name('lessons.delete');
    Route::get('/lessons/show/{id}', [LessonController::class, 'details'])->name('lessons.details');

    Route::put('/lessons/edit/{id}/cancel', [LessonController::class, 'cancel'])->middleware('can:admin')->name('lessons.cancel');
    Route::put('/lessons/edit/{id}/activate', [LessonController::class, 'activate'])->name('lessons.activate');

    //Registration
    Route::put('/lessons/registration/update/{id}', [RegistrationController::class, 'update'])->name('lessons.registration.update');

    //Student
    Route::put('/students/{id}/update-credits', [StudentController::class, 'updateCredits'])->name('students.updateCredits');
    route::put('/students/{id}/extend-valid-date', [StudentController::class, 'extendValidDate'])->name('students.extendValidDate');
    Route::delete('/students/delete/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::put('/students/{id}/role', [StudentController::class, 'updateRole'])
    ->middleware('can:admin')
    ->name('users.updateRole');


    //Payment
    Route::get('/payment-info', [PaymentInfoController::class, 'index'])->name('payment.info.index');
    Route::get('/payment-info/create', [PaymentInfoController::class, 'create'])->name('payment.info.create');
    Route::post('/payment-info', [PaymentInfoController::class, 'store'])->name('payment.info.store');
    Route::get('/payment-info/{id}/edit', [PaymentInfoController::class, 'edit'])->name('payment.info.edit');
    Route::put('/payment-info/{id}', [PaymentInfoController::class, 'update'])->name('payment.info.update');
    Route::delete('/payment-info/{id}', [PaymentInfoController::class, 'destroy'])->name('payment.info.destroy');

    //Transaction
    Route::put('/transactions/{transaction_id}/status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::delete('/transactions/{transaction_id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');


    //Categories
    Route::get('/categories/add/', function () {
        return view('categories.create');
    })->name('categories.form');
    Route::post('/categories/add/', [CategoryController::class, 'add'])->name('categories.create');
    Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');
});


