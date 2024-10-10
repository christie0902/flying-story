<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Lesson;
use App\Models\LessonRegistration;

class PaymentController extends Controller
{
    public function showBuyCreditsPage(Request $request, $lessonId = null)
    {
        $lesson = null;
        if ($lessonId) {
            $lesson = Lesson::find($lessonId);
        }

        $paymentInfo = PaymentInfo::where('type', 'credits')->get()->map(function ($info) {
            // Add a formatted price attribute to each PaymentInfo instance
            $info->formatted_price = $info->formatted_price;
            return $info;
        });

        $user = auth()->user();
        $userProfile = $user->profile;

        return view('payment.buy-credits', compact('paymentInfo', 'userProfile', 'lesson'));
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_info_id' => 'required|exists:payment_info,id',
        ]);

        // Store the transaction data
        Transaction::create([
            'user_id' => $request->user_id,
            'payment_info_id' => $request->payment_info_id,
            'payment_status' => 'pending',
            'payment_date' => now(),
        ]);

        // Redirect to the calendar page with a success message
        return redirect()->route('calendar.show')->with('success', 'Thank you for purchasing credits. We will add credits to your account after confirming the transaction.');
    }

    public function registerForLesson(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $userId = $request->input('user_id');

        $lesson = Lesson::find($lessonId);

        $registration = new LessonRegistration([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'registration_date' => now(),
            'confirmation_status' => 'pending',
        ]);

        $registration->save();

        return redirect()->route('calendar.show')->with('success', 'Thank you for purchasing credits. We will add credits to your account and register for the class you chose after confirming the transaction!');
    }
}
