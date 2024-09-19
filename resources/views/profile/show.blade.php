@extends('layout.layout')

@section('content')
@if(session('success_message'))
<div class="alert alert-success">
    {{ session('success_message') }}
</div>
@endif

<h1>Profile</h1>
<a href="{{ route('profile.editForm') }}">Edit</a>
<p><strong>First Name:</strong> {{ $profile->first_name }}</p>
<p><strong>Last Name:</strong> {{ $profile->last_name }}</p>
<p><strong>Email:</strong> {{ $profile->email }}</p>
<p><strong>Phone:</strong> {{ $profile->phone ?? '' }}</p>
<p><strong>Credits:</strong> {{ $profile->credits }}</p>
@if ($profile->credits !== 0)
<p><strong>Credits Purchased Date:</strong> {{ $profile->credits_purchased_date}}</p>
<p><strong>Valid Date:</strong> {{ $profile->valid_date}}</p>
@endif

<p><strong>Payment Variable:</strong> {{ $profile->payment_variable }}</p>

{{-- Change password --}}
<a href="{{ route('password.changeForm') }}">Change Password</a>
@endsection