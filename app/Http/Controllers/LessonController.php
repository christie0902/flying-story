<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Recurrence;
use App\Models\LessonRegistration;
use App\Models\User;

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
    public function storeLesson(Request $request)
    {
        $lesson = Lesson::create($request->all());
      //Add logic to recurrence
        return redirect()->route('lessons.lessonList')->with('success', 'Lesson created successfully.');
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
