@extends('layout.layout')

@section('content')
<div class="container">
    <h2>Add Payment Info</h2>
    <form action="{{ route('payment_info.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" name="type" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" required step="0.01">
        </div>
        <div class="form-group">
            <label for="bank_info">Bank Info</label>
            <input type="text" name="bank_info" class="form-control">
        </div>
        <div class="form-group">
            <label for="payment_QR_url">Payment QR URL</label>
            <input type="text" name="payment_QR_url" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
