@extends('layout.layout')

@section('content')

<h1>Profile</h1>

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

@endsection