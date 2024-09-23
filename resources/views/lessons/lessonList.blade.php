@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Lessons</h1>
    <a href="{{ route('lessons.create') }}" class="btn btn-primary">Add New Class</a>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Schedule</th>
                <th>Duration (mins)</th>
                <th>Capacity</th>
                <th>Registered Students</th>
                <th>Price</th>
                <th>Status</th>
                <th>Recurrence</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lessons as $lesson)
                <tr>
                    <td>{{ $lesson->category }}</td>
                    <td>{{ $lesson->description }}</td>
                    <td>{{ $lesson->formatted_schedule }}</td>
                    <td>{{ $lesson->formatted_duration }}</td>
                    <td>{{ $lesson->capacity }}</td>
                    <td>{{ $lesson->registered_students }}</td>
                    <td>{{ $lesson->formatted_price }}</td>
                    <td>{{ $lesson->status }}</td>
                    <td>{{ $lesson->recurrence ? $lesson->recurrence->frequency : 'None' }}</td>
                    <td>
                        <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-secondary">Edit</a>
                        <a href="{{ route('lessons.cancel', $lesson->id) }}" class="btn btn-warning">Cancel</a>
                        <form action="{{ route('lessons.delete', $lesson->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
