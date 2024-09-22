<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flying Story</title>
</head>
<body>
    <a href="#">Calendar</a>

    @can('admin')
    <a href="{{route('lesson.list')}}">Class Management</a>
    <a href="#">Student Management</a>
    <a href="#">Payment Management</a>
    @endcan

    @auth
    <a href="{{ route('profile.load') }}">Profile</a>
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button>Logout</button>
    </form>
    @else
        <a href="{{ route('register') }}">Register</a>
        <a href="{{ route('login')}}">Login</a>
    @endauth


    </body>

     <!-- Page Content -->
     <div id="content">
        @yield('content')
    </div>
</body>
</html>