@extends('layout.layout')

@section('content')
@if(session('success'))
  <div class="alert alert-success">
      {{ session('success') }}
  </div>
@endif

<div class="container">
    <h1>Lessons</h1>
    <a href="{{ route('lessons.create') }}" class="btn btn-primary">Add New Class</a>

     <!-- Filter Form -->
     <form method="GET" action="{{ route('lesson.list') }}" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="month">Month</label>
                <select name="month" id="month" class="form-control">
                    <option value="">Select Month</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == $category ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 align-self-end">
                <button type="submit" class="btn btn-success">Filter</button>
                <a href="{{ route('lesson.list') }}" class="btn btn-secondary">Refresh Filter</a>
            </div>
        </div>
    </form>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Schedule</th>
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
                    <td>{{ $lesson->registered_students }} / {{ $lesson->capacity }}</td>
                    <td>{{ $lesson->status }}</td>
                    <td>{{ $lesson->recurrence_option ?? 'None' }}</td>
                    <td>
                        <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-secondary">Edit</a>

                    {{-- Activate & cancel lesson --}}
                        @if($lesson->status == "active")
                        <!-- Cancel Button -->
                            <form action="{{ route('lessons.cancel', $lesson->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to cancel this class?')">Cancel</button>
                            </form>
                        @elseif($lesson->status == "canceled")
                        <!-- Activate Button -->
                            <form action="{{ route('lessons.activate', $lesson->id) }}"method="POST" style="display:inline;">
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
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
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