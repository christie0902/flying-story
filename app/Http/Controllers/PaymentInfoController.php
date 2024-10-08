<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentInfo;
use App\Models\Transaction;

class PaymentInfoController extends Controller
{
    public function index()
    {
        $paymentInfos = PaymentInfo::all();
        $transactions = Transaction::with('user', 'paymentInfo')
            ->orderByRaw("FIELD(payment_status, 'Pending') DESC") // Prioritize 'Pending'
            ->orderBy('payment_date', 'desc')                    // Order by latest payment date
            ->paginate(10);

        return view('payment.paymentList', compact('paymentInfos', 'transactions'));
    }

    public function create()
    {
        return view('payment.payment-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'amount_of_credits' => 'required|numeric',
            'price' => 'required|numeric',
            'bank_info' => 'nullable|string|max:255',
            'payment_QR_url' => 'nullable|string|max:255',
        ], [
            'type.required' => 'The payment type field is required.',
            'type.string' => 'The payment type must be a string.',
            'type.max' => 'The payment type may not be greater than 255 characters.',

            'amount_of_credits.required' => 'Please enter the number of credits. You can enter 0 if it is not applicable',

            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',

            'bank_info.string' => 'The bank information must be a string.',
            'bank_info.max' => 'The bank information may not be greater than 255 characters.',

            'payment_QR_url.string' => 'The payment QR URL must be a string.',
            'payment_QR_url.max' => 'The payment QR URL may not be greater than 255 characters.',
        ]);

        PaymentInfo::create($request->all());
        return redirect()->route('payment.info.index')->with('success', 'Payment info created successfully.');
    }

    public function edit($id)
    {
        $paymentInfo = PaymentInfo::findOrFail($id);
        return view('payment.payment-edit', compact('paymentInfo'));
    }

    public function update(Request $request, $id)
    {
        $paymentInfo = PaymentInfo::findOrFail($id);
        $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
            'amount_of_credits' => 'required|numeric',
            'bank_info' => 'nullable|string|max:255',
            'payment_QR_url' => 'nullable|string|max:255',
        ], [
            'type.required' => 'The payment type field is required.',
            'type.string' => 'The payment type must be a string.',
            'type.max' => 'The payment type may not be greater than 255 characters.',

            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',

            'bank_info.string' => 'The bank information must be a string.',
            'bank_info.max' => 'The bank information may not be greater than 255 characters.',

            'payment_QR_url.string' => 'The payment QR URL must be a string.',
            'payment_QR_url.max' => 'The payment QR URL may not be greater than 255 characters.',
        ]);;

        $paymentInfo->update($request->all());

        if ($request->input('update_all') === 'yes') {
            PaymentInfo::where('id', '!=', $id)->update(['bank_info' => $request->bank_info]);
        }

        return redirect()->route('payment.info.index')->with('success', 'Payment info updated successfully.');
    }

    public function destroy($id)
    {
        $paymentInfo = PaymentInfo::findOrFail($id);
        $paymentInfo->delete();
        return redirect()->route('payment.info.index')->with('success', 'Payment info deleted successfully.');
    }
}
