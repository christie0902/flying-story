<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentInfo;

class PaymentInfoController extends Controller
{
    public function index()
    {
        $paymentInfos = PaymentInfo::all();
        return view('payment.paymentList', compact('paymentInfos'));
    }

    public function create()
    {
        return view('payment.payment-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
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
        ]);

        PaymentInfo::create($request->all());
        return redirect()->route('payment.paymentList')->with('success', 'Payment info created successfully.');
    }

    public function edit(PaymentInfo $paymentInfo)
    {
        return view('payment.payment-edit', compact('paymentInfo'));
    }

    public function update(Request $request, PaymentInfo $paymentInfo)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
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
        return redirect()->route('payment.paymentList')->with('success', 'Payment info updated successfully.');
    }

    public function destroy(PaymentInfo $paymentInfo)
    {
        $paymentInfo->delete();
        return redirect()->route('payment.paymentList')->with('success', 'Payment info deleted successfully.');
    }
}
