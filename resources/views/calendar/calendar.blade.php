@extends('layout.layout')

@section('content')
@if(session('success'))
  <div class="alert alert-success">
      {{ session('success') }}
  </div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

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
               
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> --}}
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Title -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Title</h6>
                            <p id="lessonTitle"></p>
                        </div>
                        <hr>

                        <!-- Category -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Category</h6>
                            <p id="lessonCategory"></p>
                        </div>
                        <hr>

                        <!-- Description -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Description</h6>
                            <p id="lessonDescription"></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Schedule -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Schedule</h6>
                            <p id="lessonSchedule"></p>
                        </div>
                        <hr>

                        <!-- Duration -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Duration</h6>
                            <p id="lessonDuration"></p>
                        </div>
                        <hr>

                        <!-- Capacity -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Capacity</h6>
                            <p id="lessonCapacity"></p>
                        </div>
                        <hr>

                        <!-- Registered Students -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Joined Students</h6>
                            <p id="lessonRegisteredStudents"></p>
                        </div>
                    </div>
                </div>

             
               {{-- User Role and Credit Status Logic --}}
            @if(auth()->check())
               @if(auth()->user()->role == 'student')
               {{-- display Workshop Sign up button --}}
                   @php
                       $credits = auth()->user()->profile->credits ?? 0;
                       $expirationDate = auth()->user()->profile->valid_date ?? now();
                   @endphp
           
                <div class="mt-3 d-flex flex-column align-items-center" id="join-cancel-container">
                    @if($credits > 0 && $expirationDate > now())
                        <p class="text-primary">Remaining credits: {{ $credits }}</p>
                 <!-- Join and Cancel buttons -->
                         {{-- Join Button Form --}}
                         <form id="joinForm" action="" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="lesson_id" id="joinLessonId">
                            <button type="submit" class="btn btn-primary px-5" onclick="return confirm('Do you want to use 1 credit to join this class?')">Join</button>
                        </form>

                        {{-- Cancel Button Form (Initially Hidden) --}}
                        <form id="cancelForm" action="" method="POST" style="display: none;">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="lesson_id" id="cancelLessonId">
                            <button type="submit" class="btn btn-warning px-5" onclick="return confirm('Are you sure you want to cancel? You will receive 1 credit back.')">Cancel Registration</button>
                        </form>

                        {{-- Warning for canceling within 12 hours --}}
                        <p id="cancelWarning" style="display: none;" class="text-danger">
                            You can't cancel this class 12 hours before it starts. Please contact us if you have any questions.
                        </p>
                        {{-- Buy credits --}}
                    @elseif($credits <= 0)
                        <p class="text-primary">You have no credits.</br>please purchase credits to register for the class.</p>
                        <a href="" id="buyCreditsButton" ><button class="btn btn-primary px-5">Buy Credits</button></a>
                    @elseif($credits > 0 && $expirationDate <= now())
                        <p class="text-info">You have {{ $credits }} credits but they have expired.</br> Please contact us for more information.</p>
                        <button id="expiredCreditsButton" class="btn btn-secondary px-5" disabled>Join</button>
                    @endif
                </div>
                <div class="workshop-button"></div>
               @endif
               {{-- Workshop button element --}}
               
           @else
               {{-- User is not logged in --}}
               <div class="mt-3 d-flex align-items-center flex-column">
                    <p class="text-info">Please log in/register to join classes.</p>
                    <a href="{{ route('login') }}" class="btn btn-info px-5 ms-2">Log In</a>
                 </div>
           @endif
           {{-- Passed lesson --}}
            <div class="mt-3 d-flex flex-column align-items-center" id="lessonPassedMessage" style="display: none;">
                <p class="text-danger">This lesson has already passed.</p>
            </div>
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
