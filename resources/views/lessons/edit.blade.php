@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Edit Lesson {{$lesson->category}}</h1>

    <form action="{{ route('lessons.update', $lessonOccurrence->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" class="form-control" value="{{ $lessonOccurrence->lesson->category }}" required>
        </div>

        <div class="form-group">
            <label for="scheduled_at">Scheduled At</label>
            <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ \Carbon\Carbon::parse($lessonOccurrence->scheduled_at)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" value="{{ $lessonOccurrence->lesson->price }}" required>
        </div>

        <div class="form-group">
            <label for="duration">Duration (minutes)</label>
            <input type="number" name="duration" class="form-control" value="{{ $lessonOccurrence->lesson->duration }}" required>
        </div>

        <div class="form-group">
            <label for="level">Level</label>
            <select name="level" class="form-control" required>
                <option value="beginner" {{ $lessonOccurrence->lesson->level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="lower-intermediate" {{ $lessonOccurrence->lesson->level == 'lower-intermediate' ? 'selected' : '' }}>Lower Intermediate</option>
                <option value="intermediate" {{ $lessonOccurrence->lesson->level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="upper-intermediate" {{ $lessonOccurrence->lesson->level == 'upper-intermediate' ? 'selected' : '' }}>Upper Intermediate</option>
                <option value="advanced" {{ $lessonOccurrence->lesson->level == 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
        </div>

        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" name="capacity" class="form-control" value="{{ $lessonOccurrence->lesson->capacity }}" required>
        </div>

        <!-- Recurrence Input (Only show if the lesson has recurrence) -->
        @if($lessonOccurrence->lesson->recurrence_id)
            <div class="form-group">
                <label for="apply_recurrence">Apply Changes to:</label>
                <select name="apply_recurrence" class="form-control">
                    <option value="current">Current Lesson Only</option>
                    <option value="future">All Future Lessons</option>
                </select>
            </div>
        @endif

        <button type="submit" class="btn btn-success">Save Changes</button>

    </form>
</div>
@endsection
