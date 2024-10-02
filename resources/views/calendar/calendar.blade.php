@extends('layout.layout')

@section('content')
{{-- Filter --}}
<div class="filter">
    <h2 class="mt-2 page-title">Class Schedule</h2>
    <form id="categoryFilterForm">
        <div class="mb-3">
            <label for="categorySelect" class="form-label">Select Category Filter</label>
            <select class="form-select" name="categories" id="categorySelect">
                <option value="">See All</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>


{{-- Render Calendar --}}
<div id="calendar"></div>

{{-- Details Modal Screen --}}
<div class="modal fade" id="lessonDetailsModal" tabindex="-1" aria-labelledby="lessonDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lessonDetailsLabel">Class Details</h5>
               
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Title:</strong> <span id="lessonTitle"></span></p>
                <p><strong>Category:</strong> <span id="lessonCategory"></span></p>
                <p><strong>Schedule:</strong> <span id="lessonSchedule"></span></p>
                <p><strong>Duration:</strong> <span id="lessonDuration"></span></p>
                <p><strong>Price:</strong> <span id="lessonPrice"></span></p>
                <p><strong>Capacity:</strong> <span id="lessonCapacity"></span></p>
                <p><strong>Registered Students:</strong> <span id="lessonRegisteredStudents"></span></p>
                <p><strong>Status:</strong> <span id="lessonStatus"></span></p>
                <p><strong>Description:</strong> <span id="lessonDescription"></span></p>
            </div>
            <div class="modal-footer">
                @can('admin')
                <a id="editButton" href="" class="btn btn-info">Edit class</a>
                @endcan
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/app.js')
@endsection
