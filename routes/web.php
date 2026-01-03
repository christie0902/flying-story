<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/calendar');


//redirect to login page if session expired
//Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'loadProfile'])->middleware('auth')->name('profile.load');
    Route::get('/profile/edit', [ProfileController::class, 'editForm'])->middleware('auth')->name('profile.editForm');
    Route::put('/profile/edit', [ProfileController::class, 'updateProfile'])->middleware('auth')->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.changeForm');


    //Calendar route
    Route::get('/calendar/load-events', [CalendarController::class, 'loadLessons'])->name('calendar.load');
    Route::get('/calendar', [CalendarController::class, 'showCalendar'])->name('calendar.show');
    Route::get('/calendar/lesson/{id}', [CalendarController::class, 'getLessonDetails'])->name('calendar.lesson');

    //Lesson registration route
    Route::post('/lessons/{lesson}/register', [RegistrationController::class, 'register'])->name('class.register');
    Route::post('/lessons/{lesson}/cancel', [RegistrationController::class, 'cancel'])->name('class.cancel');

    //Credits purchase
    Route::get('/buy-credits/{lesson_id?}', [PaymentController::class, 'showBuyCreditsPage'])->name('buy.credits');
    Route::get('/join-class/{lesson_id?}', [PaymentController::class, 'showPaymentPage'])->name('payment.class');
    Route::post('/confirm-payment', [PaymentController::class, 'confirmPayment'])->name('confirm.payment');
    Route::post('/confirm-payment/register-lesson', [PaymentController::class, 'registerForLesson'])->name('payment.register.lesson');

    //Privacy Policy
    Route::get('/privacy-policy', function () {
        return view('auth.privacy-policy');
    })->name('privacy-policy');
//});