@extends('layout.layout')

@section('content')
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="container w-100 w-lg-50" style="max-width: 100vw;">
    <h2 class="page-title">Add New Class</h1>

    <form action="{{ route('lessons.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label">Category</label>
            <select id="category-select" name="category" class="form-control" required>
                @foreach ($categories as $category)
                    <option value={{ $category->id }} {{ old('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                    </option>
                @endforeach
                    <option value=999>Other</option>
            </select>
            <input type="text" id="other-category" name="other_category" class="form-control mt-2 d-none" placeholder="Enter new category">
        </div>

        <div class="form-group">
            <label class="form-label">Payment Term</label>
            <select name="payment_type" class="form-control" required>
                <option value="">Select Payment Term for this class</option>
                @foreach ($paymentTypes as $paymentType)
                    <option value="{{ $paymentType->type }}" {{ old('payment_type') == $paymentType->type ? 'selected' : '' }}>
                        {{ $paymentType->type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Title</label>
            <textarea name="title" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Schedule</label>
            <input type="datetime-local" name="schedule" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Duration (in minutes)</label>
            <input type="number" name="duration" class="form-control" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="level">Level</label>
            <select name="level" id="level" class="form-control" required>
                <option value="beginner">Beginner</option>
                <option value="lower-intermediate">Lower Intermediate</option>
                <option value="intermediate">Intermediate</option>
                <option value="upper-intermediate">Upper Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Recurrence</label>
            <select id="recurrence-select" name="recurrence_option" class="form-control">
                <option value="">None</option>
                <option value="weekly">Weekly</option>
                <option value="bi-weekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>

        <div class="form-group" id="end-date-group" style="display: none;">
            <label class="form-label" for="end-date">End Date</label>
            <input type="date" id="end-date" name="end_date">
        </div>

        <button type="submit" class="btn btn-primary my-4 w-100">Save Class</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('category-select').addEventListener('change', function() {
        const otherCategoryInput = document.getElementById('other-category');
        const priceGroup = document.getElementById('price-group');

        if (this.value == 999) {
            otherCategoryInput.classList.remove('d-none');
            otherCategoryInput.required = true;
        } else {
            otherCategoryInput.classList.add('d-none');
            otherCategoryInput.required = false;
            otherCategoryInput.value = '';
        }
    });

    document.getElementById('recurrence-select').addEventListener('change', function() {
        const endDateGroup = document.getElementById('end-date-group');
        if (this.value) {
            endDateGroup.style.display = 'block';
        } else {
            endDateGroup.style.display = 'none';
        }
    });

</script>
@endsection