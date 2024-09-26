@extends('layout.layout')

@section('content')
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
                <th>Description</th>
                <th>Schedule</th>
                <th>Duration (mins)</th>
                <th>Level</th>
                <th>Capacity</th>
                <th>Registered Students</th>
                <th>Price</th>
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
                    <td>{{ $lesson->title }}</td>
                    <td>{{ $lesson->category->name ?? 'No Category' }}</td>
                    <td>{{ $lesson->description }}</td>
                    <td>{{ $lesson->formatted_schedule }}</td>
                    <td>{{ $lesson->formatted_duration }}</td>
                    <td>{{ $lesson->level }}</td>
                    <td>{{ $lesson->capacity }}</td>
                    <td>{{ $lesson->registered_students }}</td>
                    <td>{{ $lesson->formatted_price }}</td>
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
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to cancel this lesson?')">Cancel</button>
                            </form>
                        @elseif($lesson->status == "canceled")
                        <!-- Activate Button -->
                            <form action="{{ route('lessons.activate', $lesson->id) }}"method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to activate this lesson?')">Activate</button>
                            </form>
                        @endif

                        <form action="{{ route('lessons.delete', $lesson->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $lesson->recurrence_id }}')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                    <!-- Modal for cancel confirmation -->
                    <div class="modal fade" id="cancelModal-{{ $lesson->id }}" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel">Cancel Lesson</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to cancel this lesson?

                                    @if($lesson->recurrence_id)
                                        <div class="form-group mt-3">
                                            <label>
                                                <input type="checkbox" id="deleteAllRecurrence-{{ $lesson->id }}">
                                                Also delete all lessons in this recurrence
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                    <button type="button" class="btn btn-warning" onclick="submitCancelForm({{ $lesson->id }})">Yes, Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
    if (recurrenceId) {
        let confirmMessage = 'This lesson is part of a recurring series. Are you sure you want to delete all lessons in this series?';
        if (!confirm(confirmMessage)) {
            event.preventDefault();
            return false;
        }
    } else {
        let singleConfirmMessage = 'Are you sure you want to delete this lesson?';
        if (!confirm(singleConfirmMessage)) {
            event.preventDefault();
            return false;
        }
    }
    return true;
}
</script>
@endsection