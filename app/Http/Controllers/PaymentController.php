<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use Illuminate\Http\Request;

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
        return redirect()->route('calendar')->with('message', 'Payment confirmed. Please wait for us to confirm your transaction.');
    }
}
