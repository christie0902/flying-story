@if (count($error) > 0)
<div class="alert alert-danger">
   <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
   </ul>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    </div>

    <div>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
    </div>

    <div>
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
    </div>

    <div>
        <label for="first_name">First Name</label>
        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required>
    </div>

    <div>
        <label for="last_name">Last Name</label>
        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>
    </div>

    <div>
        <label for="phone">Phone (optional) </label>
        <input id="phone" type="text" name="phone" value="{{ old('phone') }}">
    </div>

    <button type="submit">Register</button>
</form>
