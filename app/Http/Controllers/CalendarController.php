<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;

class CalendarController extends Controller
{
    public function loadLessons()
    {
        $lessons = Lesson::all();

        $events = [];

        foreach ($lessons as $lesson) {
            $events[] = [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'start' => $lesson->schedule,
                'duration' => $lesson->duration,
                'extendedProps' => [
                    'price' => $lesson->price,
                    'description' => $lesson->description,
                ],
            ];
        }

        return response()->json($events);
    }

    public function showCalendar()
    {
        return view('calendar.calendar');
    }
}
