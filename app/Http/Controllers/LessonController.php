<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\Category;
use App\Models\User;
use App\Models\PaymentInfo;
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
            $query->where('category_id', $category);
            ;
        }

        $lessons = $query->orderByDesc('schedule')->paginate(10);

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
        $paymentTypes = PaymentInfo::select('type')->distinct()->get();
        return view('lessons.create', compact('categories', 'paymentTypes'));
    }

    // STORE NEW LESSON
    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|integer|exists:categories,id',
            'other_category' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'required|date',
            'payment_type' => 'required|string|exists:payment_info,type',
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
                'payment_type' => $validated['payment_type'],
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

            return redirect()->route('lesson.list')->with('success', 'Class created successfully.');
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
            // dd($lesson->category);
            if (!$existingLesson) {
                Lesson::create([
                    'category_id' => $lesson->category->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'schedule' => $currentDate->format('Y-m-d H:i:s'),
                    'payment_type' => $lesson->payment_type,
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
                    'category_id' => $lesson->category->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'schedule' => $currentDate->format('Y-m-d H:i:s'),
                    'payment_type' => $lesson->payment_type,
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
        $lesson = Lesson::findOrFail($id);
        $categories = Category::all();
        $paymentTypes = PaymentInfo::select('type')->distinct()->get();

        $relatedLessons = Lesson::where('recurrence_id', $lesson->recurrence_id)
            ->where('id', '!=', $lesson->id)
            ->get();

        return view('lessons.edit', compact('lesson', 'categories', 'relatedLessons', 'paymentTypes'));
    }

    // UPDATE LESSON
    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        // Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'required|date',
            'payment_type' => 'required|string|exists:payment_info,type',
            'duration' => 'required|integer|min:1',
            'level' => 'required|in:beginner,lower-intermediate,intermediate,upper-intermediate,advanced',
            'capacity' => 'required|integer',
            'edit_all_recurrence' => 'nullable|boolean'
        ]);

        // Check if changes should be applied to the entire series
        $applyToAll = $request->input('edit_all_recurrence', false) == '1';

        $newSchedule = $validated['schedule'];
        $dateTime = new \DateTime($newSchedule);
        $newTime = $dateTime->format('H:i');

        $lesson->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'schedule' => $newSchedule,
            'payment_type' => $validated['payment_type'],
            'duration' => $validated['duration'],
            'level' => $validated['level'],
            'capacity' => $validated['capacity'],
            'updated_at' => now(),
        ]);

        if ($applyToAll && $lesson->recurrence_id) {
            Lesson::where('recurrence_id', $lesson->recurrence_id)->get()->each(function ($relatedLesson) use ($validated, $newTime) {
                $currentDateTime = new \DateTime($relatedLesson->schedule);
                $currentDate = $currentDateTime->format('Y-m-d');

                // Update the related lesson's schedule to the new time, keeping its original date
                $relatedLesson->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'schedule' => $currentDate . ' ' . $newTime,
                    'payment_type' => $validated['payment_type'],
                    'duration' => $validated['duration'],
                    'level' => $validated['level'],
                    'capacity' => $validated['capacity'],
                    'updated_at' => now()
                ]);
            });

            return redirect()->route('lesson.list')->with('success', 'All classes in the series have been updated with the new information.');
        }

        return redirect()->route('lesson.list')->with('success', 'Class updated successfully.');
    }

    // DELETE LESSON
    public function deleteLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $deleteAll = $request->input('delete_all_recurrence', false);

        if ($lesson->recurrence_id && $deleteAll) {
            Lesson::where('recurrence_id', $lesson->recurrence_id)->delete();

            return redirect()->route('lesson.list')->with('success', 'All classes in the recurrence have been deleted.');
        }
        $lesson->delete();

        return redirect()->route('lesson.list')->with('success', 'Class deleted successfully.');
    }

    // SHOW DETAILS OF A LESSON
    public function details(Lesson $lesson, $id)
    {
        $lesson = Lesson::with('registrations.user.profile')->findOrFail($id);
        $categories = Category::all();
        $relatedLessons = Lesson::where('recurrence_id', $lesson->recurrence_id)
            ->where('id', '!=', $lesson->id)
            ->get();

        return view('lessons.details', compact('lesson', 'categories', 'relatedLessons'));
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
