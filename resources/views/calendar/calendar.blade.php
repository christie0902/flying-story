@extends('layout.layout')

@section('content')
{{-- Filter --}}
<div class="filter">
    <h2 class="mt-2 page-title">Class Schedule</h2>
    <form id="categoryFilterForm">
        <div class="mb-3">
            <label for="categorySelect" class="form-label">Select Category Filter</label>
            <select class="form-select" name="categories" id="categorySelect">
                <option value="">See All</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>


{{-- Render Calendar --}}
<div id="calendar"></div>

{{-- Details Modal Screen --}}
<div class="modal fade" id="lessonDetailsModal" tabindex="-1" aria-labelledby="lessonDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lessonDetailsLabel">Class Details</h5>
               
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Title:</strong> <span id="lessonTitle"></span></p>
                <p><strong>Category:</strong> <span id="lessonCategory"></span></p>
                <p><strong>Schedule:</strong> <span id="lessonSchedule"></span></p>
                <p><strong>Duration:</strong> <span id="lessonDuration"></span></p>
                <p><strong>Price:</strong> <span id="lessonPrice"></span></p>
                <p><strong>Capacity:</strong> <span id="lessonCapacity"></span></p>
                <p><strong>Registered Students:</strong> <span id="lessonRegisteredStudents"></span></p>
                <p><strong>Status:</strong> <span id="lessonStatus"></span></p>
                <p><strong>Description:</strong> <span id="lessonDescription"></span></p>

                {{-- User Role and Credit Status Logic --}}
               {{-- User Role and Credit Status Logic --}}
               @if(auth()->check())
               @if(auth()->user()->role == 'student')
                   @php
                       $credits = auth()->user()->profile->credits ?? 0;
                       $expirationDate = auth()->user()->profile->valid_date ?? now();
                   @endphp
           
                <div class="mt-3 d-flex flex-column align-items-center">
                    @if($credits > 0 && $expirationDate > now())
                        <p class="text-primary">Remaining credits: {{ $credits }}</p>
                        <button id="registerButton" class="btn btn-primary px-5">Join</button>
                    @elseif($credits <= 0)
                        <p class="text-primary">You have no credits.</br>please purchase credits to register for the class.</p>
                        <button id="buyCreditsButton" class="btn btn-primary px-5">Buy Credits</button>
                    @elseif($credits > 0 && $expirationDate <= now())
                        <p class="text-info">You have {{ $credits }} credits but they have expired.</br> Please contact us for more information.</p>
                        <button id="expiredCreditsButton" class="btn btn-secondary px-5" disabled>Join</button>
                    @endif
                </div>
                
               @endif
           @else
               {{-- User is not logged in --}}
               <div class="mt-3 d-flex align-items-center flex-column">
                    <p class="text-info">Please log in/register to join classes.</p>
                    <a href="{{ route('login') }}" class="btn btn-info px-5 ms-2">Log In</a>
                 </div>
           @endif
            </div>
            <div class="modal-footer">
                @can('admin')
                <a id="editButton" href="" class="btn btn-info">Edit class</a>
                @endcan
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/app.js')
@endsection
