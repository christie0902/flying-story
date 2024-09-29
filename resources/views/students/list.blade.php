@extends('layout.layout')

@section('content')
<div class="container">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <h1>Student Management</h1>

    <form method="GET" action="{{ route('students.index') }}" class="form-inline mb-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by Name or Payment Variable">

        <select name="filter" class="form-control">
            <option value="">All Students</option>
            <option value="zero_credits" {{ request('filter') === 'zero_credits' ? 'selected' : '' }}>Students with 0 Credits</option>
            <option value="expiring_credits" {{ request('filter') === 'expiring_credits' ? 'selected' : '' }}>Students with Expiring Credits</option>
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Credits</th>
                <th>Credits Purchased Date</th>
                <th>Valid Date</th>
                <th>Payment Variable</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    @if ($student->profile)
                    <td>
                        <a href="#" class="student-name" 
                           data-id={{$student->id}}
                           data-name="{{ $student->name }}"
                           data-email="{{ $student->email }}"
                           data-credits="{{ $student->profile->credits ?? 'N/A' }}"
                           data-credits-purchased="{{ $student->profile->credits_purchased_date ? \Carbon\Carbon::parse($student->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}"
                           data-valid-date="{{ $student->profile->valid_date ? \Carbon\Carbon::parse($student->profile->valid_date)->format('Y-m-d') : 'N/A' }}"
                           data-payment-variable="{{ $student->profile->payment_variable ?? 'N/A' }}">
                           {{ $student->name }}
                        </a>
                    </td>
                    <td>{{ $student->email }}</td>
                    
                    <td>{{ $student->profile->credits ?? 0 }}</td>
                    <td>{{ $student->profile->credits_purchased_date ? \Carbon\Carbon::parse($student->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $student->profile->valid_date ? \Carbon\Carbon::parse($student->profile->valid_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $student->profile->payment_variable }}</td>
                </tr>
                    @endif
            @empty
                <tr>
                    <td colspan="6" class="text-center">No students found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Custom Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($students->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $students->previousPageUrl() }}">Previous</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($students->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $students->nextPageUrl() }}">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>
            @endif
        </ul>
    </nav>

    <div class="text-center mt-3">
        Page {{ $students->currentPage() }} of {{ $students->lastPage() }}
    </div>
</div>

<!-- Modal for displaying student details -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="modal-student-name"></span></p>
                <p><strong>Email:</strong> <span id="modal-student-email"></span></p>
                <p>
                    <strong>Credits:</strong> 
                    <span id="modal-student-credits-display"></span>
                    <button id="edit-credits-btn" type="button" class="btn btn-sm btn-secondary">Edit</button>
                    {{-- Credit edit form --}}
                    <form id="edit-credits-form" method="POST" action="" style="display: none;">
                        @csrf
                        @method('PUT')
                        <input type="number" name="credits" id="modal-student-credits" class="form-control" min="0">
                        <button type="submit" class="btn btn-primary mt-2">Update Credits</button>
                    </form>
                </p>

                <!-- Input for Editing Credits -->
                {{-- <div id="edit-credits-section" style="display: none;">
                    <input type="number" id="edit-credits-input" class="form-control" min="0" placeholder="Enter new credits">
                    <button id="save-credits-btn" class="btn btn-success btn-sm mt-2">Save</button>
                    <button id="cancel-credits-btn" class="btn btn-danger btn-sm mt-2">Cancel</button>
                </div> --}}

                <p><strong>Credits Purchased Date:</strong> <span id="modal-credits-purchased"></span></p>
                <p><strong>Valid Date:</strong> <span id="modal-valid-date"></span></p>
                <p><strong>Payment Variable:</strong> <span id="modal-payment-variable"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentNames = document.querySelectorAll('.student-name');
        const modal = document.getElementById('studentModal');
        const form = document.getElementById('edit-credits-form');
        const editCreditsBtn = document.getElementById('edit-credits-btn');
        const creditsDisplay = document.getElementById('modal-student-credits-display');
        const creditsInput = document.getElementById('modal-student-credits');

        studentNames.forEach(function(student) {
            student.addEventListener('click', (event) => {
                
                event.preventDefault();
                // Get the data attributes from the clicked student link
                const id = student.getAttribute('data-id');
                const name = student.getAttribute('data-name');
                const email = student.getAttribute('data-email');
                const credits = student.getAttribute('data-credits');
                const creditsPurchased = student.getAttribute('data-credits-purchased');
                const validDate = student.getAttribute('data-valid-date');
                const paymentVariable = student.getAttribute('data-payment-variable');

                // currentStudentId = id;

                // Populate the modal with the student's details
                document.getElementById('modal-student-name').textContent = name;
                document.getElementById('modal-student-email').textContent = email;
                creditsDisplay.textContent = credits;
                document.getElementById('modal-credits-purchased').textContent = creditsPurchased;
                document.getElementById('modal-valid-date').textContent = validDate;
                document.getElementById('modal-payment-variable').textContent = paymentVariable;

                form.action = `/admin/students/${id}/update-credits`;

                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();

                form.style.display = 'none';
            });
        });

        editCreditsBtn.addEventListener('click', () => {
            form.style.display = 'block';
            creditsDisplay.style.display = 'none';
            editCreditsBtn.style.display = 'none';
    });
});

</script>
@endsection