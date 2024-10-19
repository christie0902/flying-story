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

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body px-5">
                        <h1 class="page-title text-center mb-4">Edit Profile</h1>

                        <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- First Name -->
                            <div class="form-group mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input id="first_name" type="text" name="first_name" class="form-control" value="{{ $profile->first_name ?? old('first_name') }}" required>
                            </div>

                            <!-- Last Name -->
                            <div class="form-group mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input id="last_name" type="text" name="last_name" class="form-control" value="{{ $profile->last_name ?? old('last_name') }}" required>
                            </div>

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" class="form-control" value="{{ $profile->email ?? old('email') }}" required>
                            </div>

                            <!-- Phone -->
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Phone (Optional)</label>
                                <input id="phone" type="text" name="phone" class="form-control" value="{{ $profile->phone ?? old('phone') }}">
                            </div>

                            <!-- Credits Info -->
                            <div class="mb-3">
                                <p><strong>Credits:</strong> {{ $profile->credits }}</p>
                                @if ($profile->credits !== 0)
                                    <p><strong>Credits Purchased Date:</strong> {{ $profile->credits_purchased_date }}</p>
                                    <p><strong>Valid Until:</strong> {{ $profile->valid_date }}</p>
                                @endif
                            </div>

                            <!-- Payment Variable -->
                            <div class="mb-3">
                                <p><strong>Payment Variable:</strong> {{ $profile->payment_variable }}</p>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('profile.load') }}" class="btn btn-outline-secondary mx-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
