@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Add New Lesson</h1>

    <form action="{{ route('lessons.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label>Schedule</label>
            <input type="datetime-local" name="schedule" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Recurrence</label>
            <select name="recurrence_id" class="form-control">
                <option value="">None</option>
                @foreach($recurrences as $recurrence)
                    <option value="{{ $recurrence->id }}">{{ $recurrence->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection
