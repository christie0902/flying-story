<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Lesson;
use App\Models\LessonRegistration;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // BUY CREDITS
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

    // OTHER PAYMENT TERMS
    public function showPaymentPage(Request $request, $lessonId = null)
    {
        $lesson = null;
        if ($lessonId) {
            $lesson = Lesson::find($lessonId);
        }

        $paymentInfo = PaymentInfo::where('type', $lesson->payment_type)->get()->map(function ($info) {
            $info->formatted_price = $info->formatted_price;
            return $info;
        });

        $user = auth()->user();
        $userProfile = $user->profile;

        return view('payment.class-payment', compact('paymentInfo', 'userProfile', 'lesson'));
    }

    // CONFIRM PAYMENT
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_info_id' => 'required|exists:payment_info,id',
            'lesson_id' => 'nullable|exists:lessons,id', 
        ]);
    
        DB::beginTransaction();
    
        try {
            // Store the transaction data
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'payment_info_id' => $request->payment_info_id,
                'payment_status' => 'pending',
                'payment_date' => now(),
            ]);
    
            // If lesson_id is present, register for the class
            if ($request->lesson_id) {
                $lesson = Lesson::find($request->lesson_id);

                    if ($lesson->totalRegisteredStudentsCount() < $lesson->capacity) {
                    LessonRegistration::create([
                        'user_id' => $request->user_id,
                        'lesson_id' => $request->lesson_id,
                        'registration_date' => now(),
                        'confirmation_status' => 'pending',
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('calendar.show')->with('success', 'Thank you for your payment and registration. We will confirm the transaction as soon as possible.');
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return redirect()->route('calendar.show')->with('error', 'There was an error processing your payment. Please try again.');
        }
    }

}
