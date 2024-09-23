<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Recurrence;
use App\Models\LessonRegistration;
use App\Models\LessonOccurrence;
use App\Models\User;
use Carbon\Carbon;

class LessonController extends Controller
{
    // Show all lessons
    public function loadLessons()
    {
        $lessons = Lesson::with('recurrence')->get();
        // dd($lessons);
        return view('lessons.lessonList', compact('lessons'));
    }

    // Show form to create a new lesson
    public function createLesson()
    {
        $recurrences = Recurrence::all();
        return view('lessons.create', compact('recurrences'));
    }

    // Store a new lesson
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string',
            'schedule' => 'required|date',
            'capacity' => 'required|integer',
            'recurrence_id' => 'nullable|string',
            'end_date' => 'nullable|date',
        ]);

        $category = $request->input('category');
        if ($category === 'Other') {
            $category = $request->input('other_category');
        }

        $lesson = Lesson::create([
            'category' => $category,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'capacity' => $request->capacity,
            // Add other fields as necessary
        ]);

        // Handle recurrence if selected
        if ($request->recurrence_id && $request->end_date) {
            $this->createRecurrences($lesson, $request->recurrence_id, $request->end_date);
        }

        return redirect()->route('lessons.lessonList')->with('success', 'Lesson created successfully!');
    }

    //create recurrence function
    private function createRecurrences($lesson, $recurrenceType, $endDate)
    {
        $currentDate = Carbon::parse($lesson->schedule);
        $endDate = Carbon::parse($endDate);

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            LessonOccurrence::create([
                'lesson_id' => $lesson->id,
                'scheduled_at' => $currentDate,
            ]);

            if ($recurrenceType === 'weekly') {
                $currentDate->addWeek();
            } elseif ($recurrenceType === 'bi-weekly') {
                $currentDate->addWeeks(2);
            } elseif ($recurrenceType === 'monthly') {
                $currentDate->addMonth();
            }
        }
    }


    // Show edit form
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
}
