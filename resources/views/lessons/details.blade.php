@extends('layout.layout')

@section('content')
<div class="container mt-5">
    <h2 class="page-title text-center mb-4">Class Details: {{ $lesson->title }}</h2>
    <a href="{{ route('lessons.edit', $lesson->id) }}" class="btn btn-primary mb-4">Edit Class</a>

    <div class="row">
        <!-- Left Column (Lesson Info) -->
        <div class="col-md-6">
            <!-- Title -->
            <div class="mb-3">
                <h5 class="fw-bold">Title</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->title }}</p>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <h5 class="fw-bold">Category</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->category->name ?? 'No Category' }}</p>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <h5 class="fw-bold">Description</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->description }}</p>
            </div>

            <!-- Schedule -->
            <div class="mb-3">
                <h5 class="fw-bold">Schedule</h5>
                <p class="border p-3 rounded shadow-sm">{{ \Carbon\Carbon::parse($lesson->schedule)->format('Y-m-d H:i') }}</p>
            </div>

            <!-- Duration -->
            <div class="mb-3">
                <h5 class="fw-bold">Duration</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->formatted_duration }} mins</p>
            </div>
        </div>

        <!-- Right Column (Additional Info) -->
        <div class="col-md-6">
            <!-- Level -->
            <div class="mb-3">
                <h5 class="fw-bold">Level</h5>
                <p class="border p-3 rounded shadow-sm">{{ ucfirst($lesson->level) }}</p>
            </div>

            <!-- Capacity -->
            <div class="mb-3">
                <h5 class="fw-bold">Capacity</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->capacity }}</p>
            </div>

            <!-- Payment Term -->
            <div class="mb-3">
                <h5 class="fw-bold">Payment Term</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->payment_type }}</p>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <h5 class="fw-bold">Status</h5>
                <p class="border p-3 rounded shadow-sm">{{ ucfirst($lesson->status) }}</p>
            </div>

            <!-- Recurrence -->
            <div class="mb-3">
                <h5 class="fw-bold">Recurrence</h5>
                <p class="border p-3 rounded shadow-sm">{{ $lesson->recurrence_option ?? 'None' }}</p>
            </div>
        </div>
    </div>

    <!-- Related Lessons -->
    @if ($relatedLessons->isNotEmpty())
        <div class="mt-3">
            <h4 class="sub-title">Other Classes in this Series:</h4>
            <ul class="list-group list-group-flush">
                @foreach ($relatedLessons as $relatedLesson)
                    <li class="list-group-item">
                        {{ $relatedLesson->title }} ({{ \Carbon\Carbon::parse($relatedLesson->schedule)->format('Y-m-d H:i') }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Registered Students -->
    @if ($lesson->registrations->count() > 0)
        <div class="mt-5">
            <h4 class="sub-title">Registered Students</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        @if ($lesson->payment_type == 'credits')
                            <th>Credits</th>
                            <th>Credits Valid Until</th>
                            <th>Credits Purchased Date</th>
                        @endif
                        <th>Registration Date</th>
                        <th>Confirmation Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lesson->registrations as $registration)
                        <tr>
                            <td>{{ $registration->user->name }}</td>
                            <td>{{ $registration->user->email }}</td>
                            @if ($lesson->payment_type == 'credits')
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
                            @endif
                            <td>{{ \Carbon\Carbon::parse($registration->registration_date)->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('lessons.registration.update', $registration->id) }}" method="POST" class="confirmation-form form-label d-flex">
                                    @csrf
                                    @method('PUT')
                                    <select name="confirmation_status" onchange="confirmStatusChange(this)" class="form-select form-select-sm  w-100" style="min-width: 150px;">
                                        <option value="Confirmed" {{ $registration->confirmation_status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="Pending" {{ $registration->confirmation_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Canceled" {{ $registration->confirmation_status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-info ms-2 px-2">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mt-4">No students registered yet</p>
    @endif
</div>

<script>
    const confirmStatusChange = (select) => {
        const form = select.closest('.confirmation-form');
        const selectedValue = select.value;

        if (!select.disabled && confirm(`Are you sure you want to change the confirmation status to ${selectedValue}?`)) {
            form.submit();
        } else {
            select.value = select.options[0].value;
        }
    }
</script>
@endsection
