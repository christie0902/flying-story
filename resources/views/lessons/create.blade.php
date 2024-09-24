@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Add New Lesson</h1>

    <form action="{{ route('lessons.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label>Category</label>
            <select id="category-select" name="category" class="form-control" required>
                <option value="">Select a Category</option>
                <option value="Aerial Yoga">Aerial Yoga</option>
                <option value="Aerial Sling">Aerial Sling</option>
                <option value="Aerial Hoop">Aerial Hoop</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" id="other-category" name="other_category" class="form-control mt-2 d-none" placeholder="Enter new category">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Schedule</label>
            <input type="datetime-local" name="schedule" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Duration (in minutes)</label>
            <input type="number" name="duration" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Recurrence</label>
            <select id="recurrence-select" name="recurrence_id" class="form-control">
                <option value="">None</option>
                <option value="weekly">Weekly</option>
                <option value="bi-weekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>

        <div class="form-group" id="end-date-group" style="display: none;">
            <label for="end-date">End Date</label>
            <input type="date" id="end-date" name="end_date">
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('category-select').addEventListener('change', function() {
        const otherCategoryInput = document.getElementById('other-category');
        if (this.value === 'Other') {
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