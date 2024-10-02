@extends('layout.layout')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h2 class="page-title">Account Login</h1>
            <form action="{{ route('login') }}" method="post" class="needs-validation" novalidate>

                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" class="form-control @error('email') is-invalid @enderror" id="email" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" placeholder="Enter your password" class="form-control @error('password') is-invalid @enderror" id="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p>Don't have an account yet? <a href="{{Route('register')}}">Register here</a></p>
        </div>
    </div>
</div>
@endsection