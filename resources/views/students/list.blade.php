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

    <h2 class="page-title">Students</h2>

        <form method="GET" action="{{ route('students.index') }}" class="row g-3 mb-3">
            <div class="col-md-6 col-lg-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by Name or Payment Variable">
            </div>
        
            <div class="col-md-4 col-lg-3">
                <select name="filter" class="form-control">
                    <option value="">All Students</option>
                    <option value="zero_credits" {{ request('filter') === 'zero_credits' ? 'selected' : '' }}>Students with 0 Credits</option>
                    <option value="expiring_credits" {{ request('filter') === 'expiring_credits' ? 'selected' : '' }}>Students with Expiring Credits</option>
                </select>
            </div>
        
            <div class="col-md-2 col-lg-2">
                <button type="submit" class="btn btn-primary w-50">Search</button>
            </div>
        </form>
        
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Credits</th>
                <th>Credits Purchased Date</th>
                <th>Expiration Date</th>
                <th>Payment Variable</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    @if ($student->profile)
                    <td>
                        <a href="#" class="student-name" 
                        data-id="{{ $student->id }}"
                           data-name="{{ $student->name }}"
                           data-role="{{ $student->role }}"
                           data-email="{{ $student->email }}"
                           data-credits="{{ $student->profile->credits ?? 'N/A' }}"
                           data-credits-purchased="{{ $student->profile->credits_purchased_date ? \Carbon\Carbon::parse($student->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}"
                           data-valid-date="{{ $student->profile->valid_date ? \Carbon\Carbon::parse($student->profile->valid_date)->format('Y-m-d') : 'N/A' }}"
                           data-payment-variable="{{ $student->profile->payment_variable ?? 'N/A' }}"
                           data-recent-classes="{{ json_encode($student->classRegistrations->map(function($registration) {
                            return [
                                'lesson_name' => $registration->lesson->title ?? 'Unknown Class',
                                'lesson_schedule' => $registration->lesson->formatted_schedule ?? 'No Schedule',
                                'lesson_id' => $registration->lesson->id
                            ];
                        })) }}">
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
                    <td colspan="7" class="text-center">No students found</td>
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



    <hr class="my-5">
    <h2 class="page-title">Teachers</h2>

    <table class="table">
    <tbody>
        @forelse ($teachers as $teacher)
        @if ($teacher->profile)
            <tr>
            <td>
                <a href="#" class="student-name"
                data-id="{{ $teacher->id }}"
                data-role="{{ $teacher->role }}"
                data-name="{{ $teacher->name }}"
                data-email="{{ $teacher->email }}"
                data-credits="{{ $teacher->profile->credits ?? 'N/A' }}"
                data-credits-purchased="{{ $teacher->profile->credits_purchased_date ? \Carbon\Carbon::parse($teacher->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}"
                data-valid-date="{{ $teacher->profile->valid_date ? \Carbon\Carbon::parse($teacher->profile->valid_date)->format('Y-m-d') : 'N/A' }}"
                data-payment-variable="{{ $teacher->profile->payment_variable ?? 'N/A' }}"
                data-recent-classes='@json($teacher->classRegistrations->map(function($registration) {
                    return [
                        "lesson_name" => $registration->lesson->title ?? "Unknown Class",
                        "lesson_schedule" => $registration->lesson->formatted_schedule ?? "No Schedule",
                        "lesson_id" => $registration->lesson->id
                    ];
                }))'>
                {{ $teacher->name }}
                </a>
            </td>
            <td>{{ $teacher->email }}</td>
            <td>{{ $teacher->profile->credits ?? 0 }}</td>
            <td>{{ $teacher->profile->credits_purchased_date ? \Carbon\Carbon::parse($teacher->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $teacher->profile->valid_date ? \Carbon\Carbon::parse($teacher->profile->valid_date)->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $teacher->profile->payment_variable }}</td>
            </tr>
        @endif
        @empty
        <tr><td colspan="6" class="text-center">No teachers found</td></tr>
        @endforelse
    </tbody>
    </table>
</div>

<!-- Modal for displaying student details -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">User Details</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p><strong>Name:</strong> <span id="modal-student-name"></span></p>
                </div>

                <div class="mb-3">
                    <p><strong>Email:</strong> <span id="modal-student-email"></span></p>
                </div>

                <div class="mb-3">
                <p><strong>Role:</strong> <span id="modal-user-role-text"></span></p>
                    <form id="update-role-form" method="POST" style="display:none;">
                    @csrf
                    @method('PUT')
                    <select name="role" id="modal-role-select" class="form-control">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button type="submit" class="btn btn-primary mt-2">Update role</button>
                    </form>
                </div>

                <!-- Credit Management -->
                <div class="mb-3 border-top pt-3">
                    <p><strong>Credits:</strong> <span id="modal-student-credits-display"></span></p>
                    <button id="edit-credits-btn" type="button" class="btn btn-sm btn-secondary">Edit</button>

                    <form id="edit-credits-form" method="POST" action="" style="display: none;">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="modal-student-credits">Credits</label>
                            <input type="number" name="credits" id="modal-student-credits" class="form-control" min="0" value="">
                        </div>

                        <div class="form-group mt-3">
                            <label for="credits-purchased-date">Credits Purchased Date</label>
                            <input type="date" name="credits_purchased_date" id="credits-purchased-date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="valid-date">Valid Date</label>
                            <input type="date" name="valid_date" id="valid-date" class="form-control" value="{{ now()->addDays(45)->format('Y-m-d') }}">
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Update Credits</button>
                    </form>
                </div>

                <div class="mb-3 border-top pt-3">
                    <p><strong>Credits Purchased Date:</strong> <span id="modal-credits-purchased"></span></p>
                </div>

                <!-- Valid date extension -->
                <div class="mb-3 border-top pt-3">
                    <p><strong>Valid Date:</strong> <span id="modal-valid-date"></span></p>
                    <button id="edit-valid-date-btn" type="button" class="btn btn-sm btn-secondary">Extend</button>

                    <form id="edit-valid-date-form" method="POST" action="" style="display: none;">
                        @csrf
                        @method('PUT')
                        <input type="date" name="valid_date" id="modal-valid-date-input" class="form-control mt-2">
                        <button type="submit" class="btn btn-primary mt-2">Update</button>
                    </form>
                </div>

                <div class="mb-3 border-top pt-3">
                    <p><strong>Payment Variable:</strong> <span id="modal-payment-variable"></span></p>
                </div>

                {{-- Recent classes --}}
                <div class="mb-3 border-top pt-3">
                    <h5>Recent Classes</h5>
                    <ul id="modal-recent-classes" class="list-group">
                    </ul>
                </div>

                <form id="delete-account-form" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mt-2" id="delete-account-btn" onclick="return confirm('Warning: Are you sure you want to delete this student account? All data, including credits and class history, will be permanently removed.')">Delete Student's Account</button>
                </form>
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
        const purchaseDateInput = document.getElementById('credits-purchased-date');
        const validDateInput = document.getElementById('valid-date');
        const extendValidInput=document.getElementById('modal-valid-date-input');
        const validDateSpan = document.getElementById('modal-valid-date');
        const editValidDateBtn = document.getElementById('edit-valid-date-btn');
        const editValidDateForm = document.getElementById('edit-valid-date-form');
       

        const calculateValidDate = (purchaseDate) => {
            const date = new Date(purchaseDate);
            date.setDate(date.getDate() + 45);
            
            return date.toISOString().split('T')[0]; 
            // Return in YYYY-MM-DD format
        }

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
                const role = student.getAttribute('data-role') || 'student';
                const recentClasses = JSON.parse(student.getAttribute('data-recent-classes'));
                

                // Populate the modal with the student's details
                document.getElementById('modal-student-name').textContent = name;
                document.getElementById('modal-student-email').textContent = email;

                // Set role
                const roleText = document.getElementById('modal-user-role-text');
                if (roleText) roleText.textContent = role;

                // Update role form (admin)
                const updateRoleForm = document.getElementById('update-role-form');
                const roleSelect = document.getElementById('modal-role-select');

                if (updateRoleForm && roleSelect) {
                    updateRoleForm.style.display = 'block';
                    updateRoleForm.action = `/admin/students/${id}/role`;
                    roleSelect.value = role;
}


                creditsDisplay.textContent = credits;
                validDateSpan.textContent = validDate;
                validDateInput.value = validDate;
                creditsInput.value= credits;
                extendValidInput.value=validDate;
                document.getElementById('modal-payment-variable').textContent = paymentVariable;

                // Account Delete
                const deleteAccountForm = document.getElementById('delete-account-form');
                deleteAccountForm.action = `/admin/students/delete/${id}`;

                form.action = `/admin/students/${id}/update-credits`;
                editValidDateForm.action = `/admin/students/${id}/extend-valid-date`;

                // Recent class
                const recentClassesList = document.getElementById('modal-recent-classes');
                recentClassesList.innerHTML = '';
                if (recentClasses.length > 0) {
                    recentClasses.forEach(classData => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';

                        const lessonLink = document.createElement('a');
                        lessonLink.href = `/admin/lessons/show/${classData.lesson_id}`;
                        lessonLink.textContent = classData.lesson_name;
                        lessonLink.className = 'text-decoration-none';
                        li.appendChild(lessonLink);
                        const scheduleSpan = document.createElement('span');
                        scheduleSpan.className = 'badge badge-primary badge-pill text-dark';
                        scheduleSpan.textContent = classData.lesson_schedule;
                        li.appendChild(scheduleSpan);

                        recentClassesList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No recent confirmed classes found.';
                    recentClassesList.appendChild(li);
                }

                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();

                form.style.display = 'none';
                editValidDateForm.style.display = 'none';

            });
        });

        editValidDateBtn.addEventListener('click', () => {
            editValidDateForm.style.display = 'block';
            editValidDateBtn.style.display = 'none';
        });

                // Confirm valid date change before submitting
        editValidDateForm.addEventListener('submit', (event) => {
            const confirmed = confirm("Are you sure you want to extend the student's valid date?");
             if (!confirmed) {
                 event.preventDefault();
            }
        });

        // Confirm changes in role
        const updateRoleForm = document.getElementById('update-role-form');
        if (updateRoleForm) {
            updateRoleForm.addEventListener('submit', (event) => {
                const confirmed = confirm("Are you sure you want to change this user's role?");
                if (!confirmed) event.preventDefault();
            });
}

        editCreditsBtn.addEventListener('click', () => {
            form.style.display = 'block';
            creditsDisplay.style.display = 'none';
            editCreditsBtn.style.display = 'none';
        });

        // Update the valid date when the credits purchased date is changed
        purchaseDateInput.addEventListener('change', function() {
            const purchaseDate = new Date(this.value);
            const validDate = calculateValidDate(purchaseDate);
            validDateSpan.textContent = validDate;
            validDateInput.value = validDate;
        });

        // Confirm credit changes before submitting
        form.addEventListener('submit', (event) => {
            const confirmed = confirm("Are you sure you want to update the student's credits?");
            if (!confirmed) {
                event.preventDefault();
            }
        });
        modal.addEventListener('hidden.bs.modal', () => {      
            form.style.display = 'none';
            editCreditsBtn.style.display = 'block';
            editValidDateForm.style.display = 'none';
            editValidDateBtn.style.display = 'block';
            creditsDisplay.style.display = 'inline';
        });
    });

       

</script>
@endsection