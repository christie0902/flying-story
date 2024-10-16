@extends('layout.layout')

@section('content')
    <h2>Edit Category</h2>

    <form method="POST" action="{{ route('categories.update', $category->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
        </div>

        <div class="mb-3">
            <label for="bg_color" class="form-label">Background Color</label>
            <input type="text" class="form-control" id="bg_color" name="bg_color" value="{{ $category->bg_color }}" required>
        </div>

        <div class="mb-3">
            <label for="img_url" class="form-label">Image URL</label>
            <input type="url" class="form-control" id="img_url" name="img_url" value="{{ $category->img_url }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Category</button>
        <a href="{{ route('lesson.list') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
