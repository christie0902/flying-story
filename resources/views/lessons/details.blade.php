@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Registrations for {{ $lesson->category }}</h1>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Credits</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
                <tr>
                    <td>{{ $registration->user->profile->first_name }}</td>
                    <td>{{ $registration->user->profile->last_name }}</td>
                    <td>{{ $registration->user->profile->email }}</td>
                    <td>{{ $registration->user->profile->credits }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
