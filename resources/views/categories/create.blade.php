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
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <h2 class="page-title">Add New Category</h2>

            <form action="{{ route('categories.add') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <textarea name="name" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Background Color</label>
                    <textarea name="bg_color" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <textarea name="img_url" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary my-4 w-100">Save Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
