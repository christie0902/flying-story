@extends('layout.layout')
{{-- @push('style')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush --}}

@section('content')
@if (count($errors) > 0)
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach
</div>
@endif

<h1>Edit Profile</h1>
<a href="{{ route('profile.load') }}">Cancel</a>

<form action="{{ route('profile.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label for="first"></label>

<div>
    <label for="first_name">First Name</label>
    <input id="first_name" type="text" name="first_name" value="{{ $profile->first_name ?? old('first_name') }}" required>
</div>

<div>
    <label for="last_name">Last Name</label>
    <input id="last_name" type="text" name="last_name" value="{{ $profile->last_name ?? old('last_name') }}" required>
</div>

<div>
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ $profile->email ?? old('email') }}" required>
</div>

<div>
    <label for="phone">Phone (Optional) </label>
    <input id="phone" type="text" name="phone" value="{{ $profile->phone ?? old('phone') }}">
</div>

<p><strong>Credits:</strong> {{ $profile->credits }}</p>
@if ($profile->credits !== 0)
<p><strong>Credits Purchased Date:</strong> {{ $profile->credits_purchased_date}}</p>
<p><strong>Valid Date:</strong> {{ $profile->valid_date}}</p>
@endif
<p><strong>Payment Variable:</strong> {{ $profile->payment_variable }}</p>

<button type="submit">Save</button>
</form>

@endsection