<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- FullCalendar CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/common@latest/main.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/list@latest/main.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/sketchy@latest/main.css" rel="stylesheet" />

    <title>Flying Story</title>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Flying Story</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route ('calendar.show')}}">Calendar</a>
                    </li>

                    @can('admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lesson.list') }}">Class Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('students.index')}}">Student Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Payment Management</a>
                    </li>
                    @endcan

                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.load') }}">Profile</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="post" class="d-inline">
                            @csrf
                            <button class="btn btn-link nav-link" style="border: none; background: none; padding: 0; cursor: pointer;">
                                Logout
                            </button>
                        </form>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

    @yield('scripts')

</body>
</html>
