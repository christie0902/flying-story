<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use App\Models\Transaction;

class PaymentController extends Controller
{
    public function showBuyCreditsPage(Request $request)
    {
        $paymentInfo = PaymentInfo::where('type', 'credits')->get()->map(function ($info) {
            // Add a formatted price attribute to each PaymentInfo instance
            $info->formatted_price = $info->formatted_price;
            return $info;
        });
        $user = auth()->user();
        $userProfile = $user->profile;

        return view('payment.buy-credits', compact('paymentInfo', 'userProfile'));
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
}
