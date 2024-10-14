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
<div class="modal fade custom-modal-width custom-modal-height" id="lessonDetailsModal" tabindex="-1" aria-labelledby="lessonDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header flex-column align-items-start">
                <!-- Full-width image on top of the title -->
                <img src="\detail-cover.jfif" alt="details-cover" class="img-fluid rounded-top w-100 mb-3" style="max-height: 200px; object-fit: cover;">
                
                <h5 class="modal-title page-title" id="lessonDetailsLabel">Class Details</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Title -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Title</h6>
                            <p id="lessonTitle"></p>
                        </div>
                        <hr>

                        <!-- Category -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Category</h6>
                            <p id="lessonCategory"></p>
                        </div>
                        <hr>

                        <!-- Description -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Description</h6>
                            <p id="lessonDescription"></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Schedule -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Schedule</h6>
                            <p id="lessonSchedule"></p>
                        </div>
                        <hr>

                        <!-- Duration -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Duration</h6>
                            <p id="lessonDuration"></p>
                        </div>
                        <hr>

                        <!-- Capacity -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Capacity</h6>
                            <p id="lessonCapacity"></p>
                        </div>
                        <hr>

                        <!-- Registered Students -->
                        <div class="mb-2">
                            <h6 class="fw-bold sub-title">Reserved Students</h6>
                            <p id="lessonRegisteredStudents"></p>
                        </div>
                    </div>
                </div>

             
    {{-- User Role and Credit Status Logic --}}

            @if(auth()->check())
               @if(auth()->user()->role == 'student')
                   @php
                       $credits = auth()->user()->profile->credits ?? 0;
                       $expirationDate = auth()->user()->profile->valid_date ?? now();
                   @endphp
           
                <div class="mt-3 d-flex flex-column align-items-center" id="join-cancel-container">

                {{-- VALID CREDITS --}}
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

                {{-- CANNOT CANCEL --}}
                        {{-- Warning for canceling within 8 hours --}}
                        <div class="d-flex flex-column align-items-center">
                            <p id="cancelWarning" style="display: none;" class="text-danger">
                                You can't cancel this class 8 hours before it starts.</br>Please contact us if you have any questions.
                            </p>
                        </div>

                {{-- NO CREDITS --}}
                    @elseif($credits <= 0)
                        {{-- Cancel Button Form (Initially Hidden) --}}
                        <form id="cancelForm" action="" method="POST" class="mb-3" style="display: none;">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="lesson_id" id="cancelLessonId">
                            <button type="submit" class="btn btn-warning px-5 text-dark" onclick="return confirm('Are you sure you want to cancel? You will receive 1 credit back.')">Cancel Registration</button>
                        </form>
                         {{-- Warning for canceling within 8 hours --}}
                         <p id="cancelWarning" style="display: none;" class="text-danger">
                            You can't cancel this class 8 hours before it starts. Please contact us if you have any questions.
                        </p>
                        <p class="text-primary text-center">You have no credits.</br>Please purchase credits to register for classes.</p>
                        <a href="" id="buyCreditsButton" ><button class="btn btn-primary px-5">Buy Credits</button></a>

                {{-- EXPIRED CREDITS --}}
                    @elseif($credits > 0 && $expirationDate <= now())
                        <p class="text-info">You have {{ $credits }} credits but they have expired.</br> Please contact us for more information.</p>
                        <button id="expiredCreditsButton" class="btn btn-secondary px-5" disabled>Join</button>
                    @endif
                </div>

                {{-- WORKSHOP --}}
                <a class="workshop-button"></a>
               @endif
               {{-- Workshop button element --}}
               
           @else
               {{-- NOT LOGGED IN --}}
               <div class="mt-3 d-flex align-items-center flex-column">
                    <p class="text-info">Please log in/register to join classes.</p>
                    <a href="{{ route('login') }}" class="btn btn-info px-5 ms-2">Log In</a>
                 </div>
           @endif

                {{-- LESSON IN THE PAST --}}
            <div class="mt-3 d-flex flex-column align-items-center" id="lessonPassedMessage" style="display: none;">
                <p class="text-danger">This lesson has already passed.</p>
            </div>
            </div>

                {{-- ADMIN EDIT BUTTON --}}
            <div class="modal-footer">
                @can('admin')
                <a id="editButton" href="" class="btn btn-info">See details</a>
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
