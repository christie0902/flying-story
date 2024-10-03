@extends('layout.layout')

@section('content')
<div class="container">
    <h2>Edit Payment Info</h2>
    <form action="{{ route('payment_info.update', $paymentInfo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" name="type" class="form-control" value="{{ $paymentInfo->type }}" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" value="{{ $paymentInfo->price }}" required step="0.01">
        </div>
        <div class="form-group">
            <label for="bank_info">Bank Info</label>
            <input type="text" name="bank_info" class="form-control" value="{{ $paymentInfo->bank_info }}">
        </div>
        <div class="form-group">
            <label for="payment_QR_url">Payment QR URL</label>
            <input type="text" name="payment_QR_url" class="form-control" value="{{ $paymentInfo->payment_QR_url }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
