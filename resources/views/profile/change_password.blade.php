@extends('layout.layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body px-5">
                    <h1 class="page-title text-center mb-4">Change Password</h1>

                    <!-- Success Message -->
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Error Messages for Update Password -->
                    @if ($errors->updatePassword->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Password Change Form -->
                    <form action="{{ route('user-password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input id="current_password" type="password" name="current_password" class="form-control" required>
                        </div>

                        <!-- New Password -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('profile.load') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
