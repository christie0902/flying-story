<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    // SHOW LESSON LIST
    public function loadLessons(Request $request)
    {
        $status = $request->query('status', 'all');
        $month = $request->query('month', '');
        $category = $request->query('category', '');

        $query = Lesson::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($month) {
            $query->whereMonth('schedule', $month);
        }

        if ($category) {
            $query->where('category_id', $category);;
        }

        $lessons = $query->orderByDesc('schedule')->get();

        $categories = Category::all();

        return view('lessons.lessonList', [
            'lessons' => $lessons,
            'status' => $status,
            'month' => $month,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    // SHOW ADD LESSON FORM
    public function createLesson()
    {
        $categories = Category::all();
        return view('lessons.create', compact('categories'));
    }

    // STORE NEW LESSON
    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|integer',
            'other_category' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'required|date',
            'price' => 'nullable|numeric|required_if:category,Workshop',
            'duration' => 'required|integer|min:1',
            'level' => 'required|in:beginner,lower-intermediate,intermediate,upper-intermediate,advanced',
            'capacity' => 'required|integer',
            'recurrence_option' => 'nullable|in:weekly,bi-weekly,monthly', 
            'end_date' => 'nullable|date|after_or_equal:schedule',
        ], [
            'category.required' => 'The category field is required.',
            'category.string' => 'The category must be a string.',
            'category.max' => 'The category may not be greater than 255 characters.',

            'other_category.string' => 'The other category must be a string.',

            'title.require' => 'Title is required',

            'description.string' => 'The description must be a string.',

            'schedule.required' => 'The schedule field is required.',
            'schedule.date' => 'The schedule must be a valid date.',

            'price.required_if' => 'The price field is required when the category is Workshop.',
            'price.numeric' => 'The price must be a number.',

            'duration.required' => 'The duration field is required.',
            'duration.integer' => 'The duration must be an integer.',

            'level.required' => 'The level field is required.',
            'level.in' => 'The selected level is invalid.',

            'capacity.required' => 'The capacity field is required.',
            'capacity.integer' => 'The capacity must be an integer.',

            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be a date after or equal to the schedule date.',
        ]);


        DB::beginTransaction();

        try {
            // Handle custom category input
            $categoryId = $validated['category'];
            $category = Category::find($categoryId);
            if (!$category) {
                if (!empty($validated['other_category'])) {
                    // Create the new category
                    $newCategory = Category::create(['name' => $validated['other_category']]);
                    $categoryId = $newCategory->id;
                } else {
                    throw new \Exception('Category ID is invalid and no new category name provided.');
                }
            }
            
            // Create the lesson
            $lesson = Lesson::create([
                'category_id' => $categoryId,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'schedule' => $validated['schedule'],
                'price' => $validated['price'] ?? 270,
                'duration' => $validated['duration'],
                'level' => $validated['level'],
                'capacity' => $validated['capacity'],
                'registered_students' => 0,
                'status' => 'active',
            ]);

            // Check if a recurrence type is selected
            if (!empty($validated['recurrence_option'])) {
                if (empty($validated['end_date'])) {
                    throw new \Exception('End date is required for recurrence.');
                }
                // Create recurrence data if recurrence is selected
                $lesson->recurrence_option = $validated['recurrence_option'];
                $lesson->recurrence_id = uniqid();
                $lesson->save();

                $this->createOccurrences($lesson, $validated['recurrence_option'], $validated['end_date']);
            } else {
                $lesson->recurrence_option = null;
                $lesson->recurrence_id = null;
            }

            DB::commit();

            return redirect()->route('lesson.list')->with('success', 'Lesson created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create occurrences based on the recurrence rules.
     *
     * @param Lesson $lesson
     * @param Recurrence $recurrence
     * @return void
     */
    private function createOccurrences(Lesson $lesson, $recurrence, $endDate)
    {
        $startDate = Carbon::parse($lesson->schedule);
        $endDate = Carbon::parse($endDate);

        // Generate lessons based on recurrence type
        switch ($recurrence) {
            case 'weekly':
                $this->createWeeklyOccurrences($lesson, $startDate, $endDate, 1);
                break;
    
            case 'bi-weekly':
                $this->createWeeklyOccurrences($lesson, $startDate, $endDate, 2);
                break;
    
            case 'monthly':
                $this->createMonthlyOccurrences($lesson, $startDate, $endDate, 1);
                break;
        }
    }

    /**
     * Create weekly or bi-weekly occurrences based on recurrence rules.
     *
     * @param Lesson $lesson
     * @param Carbon $startDate
     * @param Carbon|null $endDate
     * @param int $interval
     * @return void
     */
    private function createWeeklyOccurrences(Lesson $lesson, Carbon $startDate, ?Carbon $endDate, int $interval)
    {
        $currentDate = $startDate->copy();
        $endDate = $endDate->copy()->endOfDay();


        while ($currentDate->lessThanOrEqualTo($endDate)) {
            $existingLesson = Lesson::where('title', $lesson->title)
            ->where('schedule', $currentDate->format('Y-m-d H:i:s'))
            ->first();

        if (!$existingLesson) {
            Lesson::create([
                'category_id' => $lesson->category,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'schedule' => $currentDate->format('Y-m-d H:i:s'),
                'price' => $lesson->price,
                'duration' => $lesson->duration,
                'level' => $lesson->level,
                'capacity' => $lesson->capacity,
                'recurrence_option' => $lesson->recurrence_option,
                'recurrence_id' => $lesson->recurrence_id, // Keep the same recurrence ID
                'registered_students' => 0,
                'status' => 'active',
            ]);
        }
            // Move to the next occurrence
            $currentDate->addWeeks($interval);
        }
    }

    /**
     * Create monthly occurrences based on recurrence rules.
     *
     * @param Lesson $lesson
     * @param Carbon $startDate
     * @param Carbon|null $endDate
     * @param int $interval
     * @return void
     */
    private function createMonthlyOccurrences(Lesson $lesson, Carbon $startDate, ?Carbon $endDate, int $interval)
    {
        $currentDate = $startDate->copy();
        $endDate = $endDate->copy()->endOfDay();

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            $existingLesson = Lesson::where('title', $lesson->title)
            ->where('schedule', $currentDate->format('Y-m-d H:i:s'))
            ->first();

        if (!$existingLesson) {
            Lesson::create([
                'category_id' => $lesson->category,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'schedule' => $currentDate->format('Y-m-d H:i:s'),
                'price' => $lesson->price,
                'duration' => $lesson->duration,
                'level' => $lesson->level,
                'capacity' => $lesson->capacity,
                'recurrence_option' => $lesson->recurrence_option,
                'recurrence_id' => $lesson->recurrence_id,
                'registered_students' => 0,
                'status' => 'active',
            ]);
        }
            // Move to the next occurrence
            $currentDate->addMonths($interval);
        }
    }

    // SHOW EDIT FORM
    public function editLesson(Lesson $lesson, $id)
    {
        $recurrences = Recurrence::all();
        return view('lessons.edit', compact('lesson', 'recurrences'));
    }

    // UPDATE LESSON
    public function updateLesson(Request $request, $id)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'price' => 'nullable|numeric',
            'duration' => 'required|integer',
            'level' => 'required|in:beginner,lower-intermediate,intermediate,upper-intermediate,advanced',
            'capacity' => 'required|integer',
            'apply_recurrence' => 'nullable|in:current,future',
        ]);

        DB::beginTransaction();

        try {
            $lessonOccurrence = LessonOccurrence::findOrFail($id);
            $lesson = $lessonOccurrence->lesson;

            $lessonOccurrence->update([
                'scheduled_at' => $validated['scheduled_at'],
            ]);

            $lesson->update([
                'category' => $validated['category'],
                'price' => $validated['price'] ?? $lesson->price,
                'duration' => $validated['duration'],
                'level' => $validated['level'],
                'capacity' => $validated['capacity'],
            ]);

            if ($lesson->recurrence_id && $request->apply_recurrence === 'future') {
                // Fetch all future occurrences in the recurrence that are after the current lesson's scheduled_at
                $futureOccurrences = LessonOccurrence::where('lesson_id', $lesson->id)
                    ->where('scheduled_at', '>', $lessonOccurrence->scheduled_at)
                    ->get();

                // Update all future occurrences with the new scheduled time and other details
                foreach ($futureOccurrences as $futureOccurrence) {
                    $futureOccurrence->update([
                        'scheduled_at' => $lessonOccurrence->scheduled_at,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('lesson.list')->with('success', 'Lesson occurrence updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // DELETE LESSON
    public function deleteLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
    
        $deleteAll = $request->input('delete_all_recurrence', false);
    
        if ($lesson->recurrence_id && $deleteAll) {
            Lesson::where('recurrence_id', $lesson->recurrence_id)->delete();
    
            return redirect()->route('lesson.list')->with('success', 'All lessons in the recurrence have been deleted.');
        }
        $lesson->delete();
    
        return redirect()->route('lesson.list')->with('success', 'Lesson deleted successfully.');
    }

    // Show students registered for a lesson
    public function details(Lesson $lesson, $id)
    {
        $registrations = LessonRegistration::with('user.profile')
            ->where('lesson_id', $lesson->id)
            ->get();

        return view('lessons.registrations', compact('lesson', 'registrations'));
    }

    //CANCEL & ACTIVATE LESSON
    public function cancel($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->status = 'canceled';
        $lesson->save();

        return redirect()->route('lesson.list')->with('success', 'Lesson canceled successfully.');
    }

    public function activate($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->status = 'active';
        $lesson->save();

        return redirect()->route('lesson.list')->with('success', 'Lesson activated successfully.');
    }
}
