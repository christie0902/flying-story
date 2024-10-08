<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function updateStatus(Request $request, $transaction_id)
{
    $request->validate([
        'payment_status' => 'required|string|in:pending,completed,refunded',
    ]);

    $transaction = Transaction::with('user.profile')->findOrFail($transaction_id);
    $user = $transaction->user;
    $profile = $user->profile;

    $transaction->payment_status = $request->input('payment_status');
    $transaction->save();

    if ($transaction->payment_status == 'completed') {
        if ($transaction->paymentInfo->type == 'credits') {
            $profile->credits += $transaction->paymentInfo->amount_of_credits;
            $profile->save();
            $message = 'Transaction confirmed and credits added to the student\'s account.';
        } else {
            $message = 'Transaction confirmed successfully.';
        }
    } elseif ($transaction->payment_status == 'refunded') {
        if ($transaction->paymentInfo->type == 'credits') {
            $profile->credits -= $transaction->paymentInfo->amount_of_credits;
            $profile->save();
            $message = 'Transaction refunded and credits removed from the student\'s account.';
        } else {
            $message = 'Transaction refunded successfully.';
        }
    } else {
        $message = 'Transaction status updated successfully.';
    }
    

    return redirect()->back()->with('success', $message);
}
}
