@extends('layout.layout')

@section('content')
@if(session('success'))
  <div class="alert alert-success">
      {{ session('success') }}
  </div>
@endif
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
    <h2 class="page-title">Classes Management</h1>
    <a href="{{ route('lessons.create') }}" class="btn btn-primary my-3">Add New Class</a>

<!-- Filter Form -->
<form method="GET" action="{{ route('lesson.list') }}" class="mb-4">
    <div class="row g-3 align-items-end">
        <!-- Status Filter -->
        <div class="col-md-3">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="canceled" {{ $status == 'canceled' ? 'selected' : '' }}>Canceled</option>
            </select>
        </div>
        
        <!-- Month Filter -->
        <div class="col-md-3">
            <label class="form-label" for="month">Month</label>
            <select name="month" id="month" class="form-control">
                <option value="">Select Month</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- Category Filter -->
        <div class="col-md-3">
            <label class="form-label" for="category">Category</label>
            <select name="category" id="category" class="form-control">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $cat->id == $category ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter and Refresh Buttons -->
        <div class="col-md-3 d-flex justify-content-center">
            <button type="submit" class="btn btn-success me-2">Filter</button>
            <a href="{{ route('lesson.list') }}" class="btn btn-secondary">Refresh</a>
        </div>
    </div>
</form>


    <table class="table mt-4">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Schedule</th>
                <th>Confirmed Students</th>
                <th>Registered Students</th>
                <th>Status</th>
                <th>Recurrence</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if ($lessons->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">No results found for this filter</td>
                </tr>
            @else
            @foreach($lessons as $lesson)
                <tr>
                    <td><a href="{{ route('lessons.details', $lesson->id) }}">{{ $lesson->title }}</a></td>
                    <td>{{ $lesson->category->name ?? 'No Category' }}</td>
                    <td>{{ $lesson->formatted_schedule }}</td>
                    <td>{{ $lesson->confirmed_students_count }}</td> <!-- Confirmed Students -->
                    <td>{{ $lesson->registrations()->count() }} / {{ $lesson->capacity }}</td> <!-- Registered Students -->
                    <td>{{ $lesson->status }}</td>
                    <td>{{ $lesson->recurrence_option ?? 'None' }}</td>

                    {{-- Actions --}}
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-secondary me-2">Edit</a>

                            {{-- Activate & Cancel Lesson --}}
                            @if($lesson->status == "active")
                                <!-- Cancel Button -->
                                <form action="{{ route('lessons.cancel', $lesson->id) }}" method="POST" class="me-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to cancel this class?')">Cancel</button>
                                </form>
                            @elseif($lesson->status == "canceled")
                                <!-- Activate Button -->
                                <form action="{{ route('lessons.activate', $lesson->id) }}" method="POST" class="me-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to activate this class?')">Activate</button>
                                </form>
                            @endif

                            {{-- Delete Button --}}
                            <form action="{{ route('lessons.delete', $lesson->id) }}" method="POST" 
                                onsubmit="return confirmDelete(event, '{{ $lesson->recurrence_id }}')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="delete_all_recurrence" value="0">
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Custom Pagination -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($lessons->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $lessons->previousPageUrl() }}">Previous</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($lessons->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $lessons->nextPageUrl() }}">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>
            @endif
        </ul>
    </nav>

    <!-- Optional: Display current page and total -->
    <div class="text-center mt-3">
        Page {{ $lessons->currentPage() }} of {{ $lessons->lastPage() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(event, recurrenceId) {
        let deleteAll = false;

        const singleDeleteConfirm = confirm('Are you sure you want to delete this class?');

        if (singleDeleteConfirm) {
            if (recurrenceId) {
                deleteAll = confirm('This lesson is part of a recurring series. Delete all lessons in this series? Only this lesson will be deleted if you click "No"');
            }

            const form = event.target;
            form.querySelector('input[name="delete_all_recurrence"]').value = deleteAll ? '1' : '0';
            return true;
        } else {
            event.preventDefault();
            return false;
        }
    }
</script>
@endsection