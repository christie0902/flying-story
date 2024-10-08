@extends('layout.layout')

@section('content')
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="container">
    <h2>Add Payment Info</h2>
    <form action="{{ route('payment.info.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" name="type" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="price">Number of credits (Please enter 0 if not applicable)</label>
            <input type="number" name="amount_of_credits" class="form-control" required>
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
        <a href="{{route('payment.info.index')}}" class="btn btn-secondary my-3 mx-2">Cancel</a>
    </form>
</div>
@endsection
