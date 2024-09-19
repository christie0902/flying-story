@extends('layout.layout')

@section('content')

<h1>Change Password</h1>
<a href="{{ route('profile.load') }}">Cancel</a>

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form action="{{ route('user-password.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div>
        <label for="current_password">Current Password</label>
        <input id="current_password" type="password" name="current_password" required>
    </div>

    <div>
        <label for="password">New Password</label>
        <input id="password" type="password" name="password" required>
    </div>

    <div>
        <label for="password_confirmation">Confirm New Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
    </div>

    <button type="submit">Update Password</button>
</form>

@endsection
