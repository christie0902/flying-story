<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Category;

class CalendarController extends Controller
{
    public function loadLessons(Request $request)
    {
        $categoryColors = [
            'Aerial Sling' => '#ff6666',
            'Aerial Hoop' => '#66ccff',
            'Aerial Silk' => '#99ff99',
            'Workshop' => '#ffcc99'
        ];

        $selectedCategory = $request->query('category');

        $lessons = Lesson::with('category')
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                return $query->where('category_id', $selectedCategory);
            })
            ->get();

        $events = $lessons->map(function ($lesson) use ($categoryColors) {
            $dateTime = new \DateTime($lesson->schedule);

            // Format the time
            $formattedTime = $dateTime->format('g\hia');

            return [
                'id' => $lesson->id,
                'title' => $lesson->category->name,
                'start' => $lesson->schedule,
                'formattedTime' => $formattedTime,
                'eventBgColor' => $categoryColors[$lesson->category->name] ?? '#cccccc',
                'status' => $lesson->status,
                'capacity' => $lesson->capacity,
                'registered_students' => $lesson->registered_students,
            ];
        });

        return response()->json($events);
    }



    public function showCalendar()
    {
        $categories = Category::all();

        return view('calendar.calendar', compact('categories'));
    }

    public function getLessonDetails($id)
    {
        $lesson = Lesson::with('category')->find($id);
        if (!$lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }

        return response()->json([
            'title' => $lesson->title,
            'category' => $lesson->category->name,
            'schedule' => $lesson->schedule,
            'duration' => $lesson->duration,
            'price' => $lesson->price,
            'capacity' => $lesson->capacity,
            'registered_students' => $lesson->registered_students,
            'status' => $lesson->status,
            'description' => $lesson->description,
        ]);
    }
}
