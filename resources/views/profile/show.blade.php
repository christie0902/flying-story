@extends('layout.layout')

@section('content')
    @if(session('success_message'))
        <div class="alert alert-success">
            {{ session('success_message') }}
        </div>
    @endif

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body px-5">
                        <h1 class="page-title text-center mb-4">Profile</h1>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="h4">Profile Information</span>
                            <a href="{{ route('profile.editForm') }}" class="btn btn-outline-primary px-4 btn-sm">Edit</a>
                        </div>

                        <!-- Profile Information Section -->
                        <div class="border-bottom mb-3 pb-3">
                            <p><strong>First Name:</strong> {{ $profile->first_name }}</p>
                            <p><strong>Last Name:</strong> {{ $profile->last_name }}</p>
                            <p><strong>Email:</strong> {{ $profile->email }}</p>
                            <p><strong>Phone:</strong> {{ $profile->phone ?? 'N/A' }}</p>
                            <p><strong>Credits:</strong> {{ $profile->credits }}</p>
                        </div>

                        <!-- Display Credits Purchase Info if Credits are Not Zero -->
                        @if ($profile->credits !== 0)
                            <div class="border-bottom mb-3 pb-3">
                                <p><strong>Credits Purchased Date:</strong> {{ $profile->credits_purchased_date }}</p>
                                <p><strong>Valid Until:</strong> {{ $profile->valid_date }}</p>
                            </div>
                        @endif

                        <!-- Credits Purchase CTA -->
                        @if ($profile->credits == 0)
                            <div class="border-bottom mb-3 pb-3">
                                <p class="text-info">You have no credits. Purchase from 1 credit to register for classes.</p>
                                <a href="{{ route('buy.credits') }}">
                                    <button class="btn btn-primary px-5">Buy Credits</button>
                                </a>
                            </div>
                        @endif

                        <!-- Payment Variable Section -->
                        <div class="border-bottom mb-3 pb-3">
                            <p><strong>Payment Variable:</strong> {{ $profile->payment_variable }}</p>
                        </div>

                        <!-- Change Password Section -->
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('password.changeForm') }}" class="btn btn-outline-secondary">Change Password</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
