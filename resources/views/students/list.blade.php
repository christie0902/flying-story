@extends('layout.layout')

@section('content')
<div class="container">
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
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    @if ($student->profile)
                    <td>{{ $student->profile->credits ?? 0 }}</td>
                    <td>{{ $student->profile->credits_purchased_date ? \Carbon\Carbon::parse($student->profile->credits_purchased_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $student->profile->valid_date ? \Carbon\Carbon::parse($student->profile->valid_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $student->profile->payment_variable }}</td>
                </tr>
                    @else
                    No Profile
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

    <!-- Optional: Display current page and total -->
    <div class="text-center mt-3">
        Page {{ $students->currentPage() }} of {{ $students->lastPage() }}
    </div>
</div>
@endsection
