@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Edit Lesson</h1>

    <form action="{{ route('lessons.update', $lesson->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- similar form as in the create -->
        <!-- Form fields (category, description, etc.) with prefilled data -->
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
