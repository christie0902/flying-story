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
            <div class="form-group col-md-3">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Select Category</option>
                    <option value="Aerial Yoga">Aerial Yoga</option>
                    <option value="Aerial Sling">Aerial Sling</option>
                    <option value="Aerial Hoop">Aerial Hoop</option>
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
                <th>Category</th>
                <th>Description</th>
                <th>Schedule</th>
                <th>Duration (mins)</th>
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
                    <td>{{ $lesson->category }}</td>
                    <td>{{ $lesson->description }}</td>
                    <td>{{ $lesson->formatted_schedule }}</td>
                    <td>{{ $lesson->formatted_duration }}</td>
                    <td>{{ $lesson->capacity }}</td>
                    <td>{{ $lesson->registered_students }}</td>
                    <td>{{ $lesson->formatted_price }}</td>
                    <td>{{ $lesson->status }}</td>
                    <td>{{ $lesson->recurrence ? $lesson->recurrence->frequency : 'None' }}</td>
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

                        <form action="{{ route('lessons.delete', $lesson->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection
