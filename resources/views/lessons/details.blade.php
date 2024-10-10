@extends('layout.layout')

@section('content')
<div class="container">
    <h2 class="page-title">Class Details: {{ $lesson->title }}</h2>
    <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-primary">Edit Class</a>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">Title</label>
                <p>{{ $lesson->title }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <p>{{ $lesson->category->name ?? 'No Category' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <p>{{ $lesson->description }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Schedule</label>
                <p>{{ \Carbon\Carbon::parse($lesson->schedule)->format('Y-m-d H:i') }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Duration</label>
                <p>{{ $lesson->formatted_duration }} mins</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">Level</label>
                <p>{{ ucfirst($lesson->level) }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Capacity</label>
                <p>{{ $lesson->capacity }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Term</label>
                <p>{{ $lesson->payment_type }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <p>{{ ucfirst($lesson->status) }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Recurrence</label>
                <p>{{ $lesson->recurrence_option ?? 'None' }}</p>
            </div>
        </div>
    </div>

    <!-- Show related lessons if any -->
    @if ($relatedLessons->isNotEmpty())
        <h4 class="sub-title">Other Classes in this Series:</h4>
        <ul>
            @foreach ($relatedLessons as $relatedLesson)
                <li>{{ $relatedLesson->title }} ({{ \Carbon\Carbon::parse($relatedLesson->schedule)->format('Y-m-d H:i') }})</li>
            @endforeach
        </ul>
    @endif

    <!-- Show registered students -->
    @if ($lesson->registrations->count() > 0)
        <h4 class="sub-title">Registered Students</h4>
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
                            @if($registration->user->profile->valid_date && \Carbon\Carbon::parse($registration->user->profile->valid_date)->isPast())
                                <span style="color: red;">
                                    {{ \Carbon\Carbon::parse($registration->user->profile->valid_date)->format('Y-m-d') }} (Expired)
                                </span>
                            @else
                                {{ $registration->user->profile->valid_date ? \Carbon\Carbon::parse($registration->user->profile->valid_date)->format('Y-m-d') : 'N/A' }}
                            @endif
                        </td>
                        <td>{{ $registration->user->profile->credits_purchased_date ? \Carbon\Carbon::parse($registration->user->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($registration->registration_date)->format('Y-m-d') }}</td>
                        <td>
                            <form action="{{ route('lessons.registration.update', $registration->id) }}" method="POST" class="confirmation-form form-label">
                                @csrf
                                @method('PUT')
                                <select name="confirmation_status" onchange="confirmStatusChange(this)" 
                                >
                                    <option value="Confirmed" {{ $registration->confirmation_status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="Pending" {{ $registration->confirmation_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Canceled" {{ $registration->confirmation_status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-info ms-1 px-2" 
                                    >Update</button>
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
        <p>No student registered yet</p>
    @endif
</div>

<script>
    const confirmStatusChange = (select) => {
        const form = select.closest('.confirmation-form');
        const selectedValue = select.value;

        if (!select.disabled && confirm(`Are you sure you want to change the confirmation status to ${selectedValue}? Student's credits will be changed accordingly!`)) {
            form.submit();
        } else {
            select.value = select.options[0].value;
        }
    }
</script>
@endsection
