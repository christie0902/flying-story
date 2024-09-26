@extends('layout.layout')

@section('content')
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="container">
    <h1>Edit Class</h1>

    <form action="{{ route('lessons.update', $lesson->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $lesson->title) }}" required>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select name="category" id="category" class="form-control" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $cat->id == $lesson->category_id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $lesson->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="schedule">Schedule</label>
            <input type="datetime-local" name="schedule" class="form-control" value="{{ old('schedule', \Carbon\Carbon::parse($lesson->schedule)->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="form-group">
            <label>Duration (in minutes)</label>
            <input type="number" name="duration" class="form-control" required value="{{ old('duration', $lesson->duration) }}">
        </div>

        <div class="form-group">
            <label for="level">Level</label>
            <select name="level" id="level" class="form-control" required>
                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="lower-intermediate" {{ old('level') == 'lower-intermediate' ? 'selected' : '' }}>Lower Intermediate</option>
                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="upper-intermediate" {{ old('level') == 'upper-intermediate' ? 'selected' : '' }}>Upper Intermediate</option>
                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
        </div>


        @if ($lesson->price)
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $lesson->price) }}">
        </div>
        @endif

        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity', $lesson->capacity) }}" required>
        </div>

        <!-- Checkbox to edit the entire series -->
        @if($lesson->recurrence_id)
        <div class="form-group">
            <label>
                <input type="checkbox" name="edit_all_recurrence" value="1">
                Apply changes to all classes in this series
            </label>
        </div>
        @endif

         <!-- List of related lessons if the lesson is part of a recurrence -->
         @if($relatedLessons->isNotEmpty())
            <h5>This class is part of a recurring series. Here are the other classes:</h5>
            <ul>
                @foreach($relatedLessons as $relatedLesson)
                    <li>
                        {{ $relatedLesson->title }}  ({{ \Carbon\Carbon::parse($relatedLesson->schedule)->format('Y-m-d H:i') }})
                        <a href="{{ route('lessons.edit', $relatedLesson->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                    </li>
                @endforeach
            </ul>
         @endif

        <button type="submit" class="btn btn-primary">Update Class</button>
    </form>
</div>
@endsection
