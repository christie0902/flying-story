@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Class Details: {{ $lesson->title }}</h1>
    <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-primary">Edit Class</a>

    <div class="form-group">
        <label>Title</label>
        <p>{{ $lesson->title }}</p>
    </div>

    <div class="form-group">
        <label>Category</label>
        <p>{{ $lesson->category->name ?? 'No Category' }}</p>
    </div>

    <div class="form-group">
        <label>Description</label>
        <p>{{ $lesson->description }}</p>
    </div>

    <div class="form-group">
        <label>Schedule</label>
        <p>{{ \Carbon\Carbon::parse($lesson->schedule)->format('Y-m-d H:i') }}</p>
    </div>

    <div class="form-group">
        <label>Duration</label>
        <p>{{ $lesson->formatted_duration }} mins</p>
    </div>

    <div class="form-group">
        <label>Level</label>
        <p>{{ ucfirst($lesson->level) }}</p>
    </div>

    <div class="form-group">
        <label>Capacity</label>
        <p>{{ $lesson->capacity }}</p>
    </div>

    <div class="form-group">
        <label>Price</label>
        <p>{{ $lesson->formatted_price }}</p>
    </div>

    <div class="form-group">
        <label>Status</label>
        <p>{{ ucfirst($lesson->status) }}</p>
    </div>

    <div class="form-group">
        <label>Recurrence</label>
        <p>{{ $lesson->recurrence_option ?? 'None' }}</p>
    </div>

    <!-- Show related lessons if any -->
    @if ($relatedLessons->isNotEmpty())
        <h3>Other Classes in this Series:</h3>
        <ul>
            @foreach ($relatedLessons as $relatedLesson)
                <li>{{ $relatedLesson->title }} ({{ \Carbon\Carbon::parse($relatedLesson->schedule)->format('Y-m-d H:i') }})</li>
            @endforeach
        </ul>
    @endif

    <!-- Show registered students -->
    @if ($lesson->registrations->count() > 0)
        <h3>Registered Students</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Credits</th>
                    <th>Credits Valid Until</th>
                    <th>Credits Purchased Date</th>
                    <th>Registration Date</th>
                    <th>Confirmation Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lesson->registrations as $registration)
                    <tr>
                        <td>{{ $registration->user->name }}</td>
                        <td>{{ $registration->user->email }}</td>
                        @if($registration->user->profile)
                        <td> 
                            <span style="{{ ($registration->user->profile->credits ?? 0) == 0 ? 'color: red;' : '' }}">
                                {{ $registration->user->profile->credits ?? 0 }}
                            </span>
                        </td>
                        <td>
                            {{ $registration->user->profile->valid_date ? \Carbon\Carbon::parse($registration->user->profile->valid_date)->format('Y-m-d') : 'N/A' }}
                        </td>
                        <td>{{ $registration->user->profile->credits_purchased_date ? \Carbon\Carbon::parse($registration->user->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($registration->registration_date)->format('Y-m-d') }}</td>
                        <td> 
                            <form action="{{ route('lessons.registration.update', $registration->id) }}" method="POST" class="confirmation-form">
                                @csrf
                                @method('PUT')
                                <select name="confirmation_status" onchange="confirmStatusChange(this)" 
                                @if ($registration->user->profile->credits <= 0) disabled @endif>
                                <option value="Confirmed" {{ $registration->confirmation_status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="Pending" {{ $registration->confirmation_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Canceled" {{ $registration->confirmation_status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                    
                            <button type="submit" class="btn btn-sm btn-primary" 
                                @if ($registration->user->profile->credits <= 0) disabled @endif>Update</button>
                            </form>
                        </td>
                        @else
                        <td>No profile</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
    "No student registered yet"
    @endif
</div>

<script>
    function confirmStatusChange(select) {
        const form = select.closest('.confirmation-form');
        const selectedValue = select.value;

        f (!select.disabled && confirm(`Are you sure you want to change the confirmation status to ${selectedValue}?`)) {
            form.submit();
        } else {
            select.value = select.options[0].value;
        }
    }
</script>
@endsection
