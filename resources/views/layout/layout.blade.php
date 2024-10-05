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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/journal/bootstrap.min.css">

    <title>Flying Story Schedule</title>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light p-0">
        <div class="container-fluid p-2">
            <a class="navbar-brand mt-1 ms-2" href="/">
                <img src="{{asset('logo.svg')}}" alt="Logo" width="40" height="40" class="d-inline-block align-text-top mx-2">
                Flying Story
            </a>
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
                        <a class="nav-link" href="{{ route('lesson.list') }}">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('students.index')}}">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('payment.info.index')}}">Payments</a>
                    </li>
                    @endcan
                </ul>

                <!-- Right-side links (Login/Register or Profile/Logout) -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 pe-2">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.load') }}">Profile</a>
                    </li>
                    <li class="nav-item logout-spacer">
                        <form action="{{ route('logout') }}" method="post" class="d-inline">
                            @csrf
                            <button class="btn logout-btn" style="border: none; background: none; padding: 0; cursor: pointer; line-height: normal;">
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>
