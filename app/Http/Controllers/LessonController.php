<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Recurrence;
use App\Models\LessonRegistration;
use App\Models\LessonOccurrence;
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

        $lessons = Lesson::with('recurrence', 'occurrences');

        if ($status !== 'all') {
            $lessons->where('status', $status);
        }

        if ($month) {
            $lessons->whereMonth('schedule', $month);
        }

        if ($category) {
            $lessons->where('category', $category);
        }

        $lessons = $lessons->get();

        // Create a collection to store both lessons and occurrences
        $allLessons = collect();

        foreach ($lessons as $lesson) {
            $allLessons->push($lesson);

            // If the lesson has occurrences, push them into the collection
            foreach ($lesson->occurrences as $occurrence) {
                // Clone the original lesson and modify schedule
                $occurrenceLesson = clone $lesson;
                $occurrenceLesson->schedule = $occurrence->scheduled_at;

                // Check if a lesson with the same schedule already exists
                $exists = $allLessons->contains(function ($existingLesson) use ($occurrenceLesson) {
                    return $existingLesson->schedule === $occurrenceLesson->schedule;
                });

                // Only push the lesson if it doesn't already exist
                if (!$exists) {
                    $allLessons->push($occurrenceLesson);
                }
            }
        }
        $sortedLessons = $allLessons->sortByDesc('schedule');
        return view('lessons.lessonList', [
            'lessons' => $sortedLessons,
            'status' => $status,
            'month' => $month,
            'category' => $category,
        ]);
    }

    // SHOW ADD LESSON FORM
    public function createLesson()
    {
        $recurrences = Recurrence::all();
        return view('lessons.create', compact('recurrences'));
    }

    // STORE NEW LESSON
    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'other_category' => 'nullable|string',
            'description' => 'nullable|string',
            'schedule' => 'required|date',
            'price' => 'nullable|numeric|required_if:category,Workshop',
            'duration' => 'required|integer',
            'level' => 'required|in:beginner,lower-intermediate,intermediate,upper-intermediate,advanced',
            'capacity' => 'required|integer',
            'recurrence_id' => 'nullable|in:weekly,bi-weekly,monthly',
            'end_date' => 'nullable|date|after_or_equal:schedule',
        ], [
            'category.required' => 'The category field is required.',
            'category.string' => 'The category must be a string.',
            'category.max' => 'The category may not be greater than 255 characters.',

            'other_category.string' => 'The other category must be a string.',

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
            $category = $validated['category'] === 'Other' && isset($validated['other_category'])
                ? $validated['other_category']
                : $validated['category'];

            // Create the lesson
            $lesson = Lesson::create([
                'category' => $category,
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
            if ($validated['recurrence_id']) {
                // Create recurrence data if recurrence is selected
                $recurrence = Recurrence::create([
                    'lesson_id' => $lesson->id,
                    'frequency' => $validated['recurrence_id'], // weekly, bi-weekly, or monthly
                    'days_of_week' => json_encode([Carbon::parse($validated['schedule'])->format('l')]),
                    'start_date' => $lesson->schedule,
                    'end_date' => $validated['end_date'] ?? null,
                    'interval' => $validated['recurrence_id'] === 'bi-weekly' ? 2 : 1,  // bi-weekly means interval of 2 weeks
                ]);
                $lesson->recurrence_id = $recurrence->id;
                $lesson->save();
                $this->createOccurrences($lesson, $recurrence);
            } else {
                // No recurrence: create a single occurrence for this lesson
                LessonOccurrence::create([
                    'lesson_id' => $lesson->id,
                    'scheduled_at' => $lesson->schedule,
                ]);
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
    private function createOccurrences(Lesson $lesson, Recurrence $recurrence)
    {
        $startDate = Carbon::parse($recurrence->start_date);
        $endDate = $recurrence->end_date ? Carbon::parse($recurrence->end_date) : null;
        $frequency = $recurrence->frequency;
        $interval = $recurrence->interval ?? 1;

        // Generate occurrences based on recurrence type
        switch ($frequency) {
            case 'weekly':
                $this->createWeeklyOccurrences($lesson, $startDate, $endDate, $interval);
                break;

            case 'bi-weekly':
                $this->createWeeklyOccurrences($lesson, $startDate, $endDate, $interval);
                break;

            case 'monthly':
                $this->createMonthlyOccurrences($lesson, $startDate, $endDate, $interval);
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
        $currentDate = $startDate;
        $occurrences = [];

        while (!$endDate || $currentDate->lessThanOrEqualTo($endDate)) {
            // Create an occurrence for each valid date
            LessonOccurrence::create([
                'lesson_id' => $lesson->id,
                'scheduled_at' => $currentDate->format('Y-m-d H:i:s'),
            ]);

            // Move to the next occurrence (interval in weeks)
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
        $currentDate = $startDate;

        while (!$endDate || $currentDate->lessThanOrEqualTo($endDate)) {
            LessonOccurrence::create([
                'lesson_id' => $lesson->id,
                'scheduled_at' => $currentDate->format('Y-m-d H:i:s'),
            ]);

            $currentDate->addMonths($interval);
        }
    }

    // SHOW EDIT FORM
    public function editLesson(Lesson $lesson, $id)
    {
        $recurrences = Recurrence::all();
        return view('lessons.edit', compact('lesson', 'recurrences'));
    }

    // Update a lesson
    public function updateLesson(Request $request, Lesson $lesson, $id)
    {
        $lesson->update($request->all());

        // If the lesson is part of a recurrence, ask if the user wants to update future lessons
        // if ($lesson->recurrence_id) {
        //     return view('lessons.update-recurrence', compact('lesson'));
        // }

        return redirect()->route('lessons.lessonList')->with('success', 'Lesson updated successfully.');
    }

    // Apply updates to future lessons
    // public function updateRecurrence(Request $request, Lesson $lesson)
    // {
    //     if ($request->input('apply_to_future')) {
    //         Lesson::where('recurrence_id', $lesson->recurrence_id)
    //               ->where('id', '>', $lesson->id)
    //               ->update($request->except('apply_to_future'));
    //     }

    //     return redirect()->route('lessons.lessonList')->with('success', 'Lesson updated along with future occurrences.');
    // }

    // Delete a lesson
    public function deleteLesson(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('lessons.lessonList')->with('success', 'Lesson deleted successfully.');
    }

    // Show students registered for a lesson
    public function details(Lesson $lesson, $id)
    {
        $registrations = LessonRegistration::with('user.profile')
            ->where('lesson_id', $lesson->id)
            ->get();

        return view('lessons.registrations', compact('lesson', 'registrations'));
    }

    //Cancel lesson
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
