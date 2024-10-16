<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Category;
use App\Models\LessonRegistration;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function loadLessons(Request $request)
    {
        $userId = auth()->id();

        $selectedCategory = $request->query('category');

        $lessons = Lesson::with('category')
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                return $query->where('category_id', $selectedCategory);
            })
            ->get();

        $registrations = LessonRegistration::where('user_id', $userId)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $events = $lessons->map(function ($lesson) use ($registrations) {
            $dateTime = new \DateTime($lesson->schedule);

            // Format the time
            $formattedTime = $dateTime->format('g\hiA');
            $registrationStatus = $registrations->has($lesson->id)
            ? $registrations[$lesson->id]->confirmation_status // 'Pending' or 'Confirmed'
            : null;

            return [
                'id' => $lesson->id,
                'title' => $lesson->category->name,
                'start' => $lesson->schedule,
                'formattedTime' => $formattedTime,
                'eventBgColor' => $lesson->category->bg_color ?? '#ffefea',
                'status' => $lesson->status,
                'capacity' => $lesson->capacity,
                'registered_students' => $lesson->registered_students,
                'userRegistrationStatus' => $registrationStatus,
            ];
        });
        // Log::info('Events Data:', $events->toArray());
        return response()->json($events);
    }



    public function showCalendar()
    {
        $categories = Category::all();

        return view('calendar.calendar', compact('categories'));
    }

    public function getLessonDetails($id)
    {
        $lesson = Lesson::with('category', 'paymentInfo')->find($id);
        if (!$lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }

        $user = auth()->check() ? auth()->user() : null;
        $userProfile = $user ? $user->profile : null;
        $paymentInfo = $lesson->paymentInfo;

        $userIsRegistered = false;
        if ($user) {
            $userRegistration = $lesson->registrations()
                ->where('user_id', $user->id)
                ->first();

            $userIsRegistered = $userRegistration && $userRegistration->confirmation_status === 'Confirmed';
        }

        return response()->json([
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'category' => $lesson->category->name,
                'schedule' => $lesson->schedule,
                'duration' => $lesson->duration,
                'payment_type' => $lesson->payment_type,
                'payment_info' => $paymentInfo ? [
                    'type' => $paymentInfo->type,
                    'price' => $paymentInfo->formatted_price,
                    'credits' => $paymentInfo->amount_of_credits,
                    'bank_info' => $paymentInfo->bank_info,
                    'payment_QR_url' => $paymentInfo->payment_QR_url,
                ] : null,
                'capacity' => $lesson->capacity,
                'registered_students' => $lesson->registered_students,
                'status' => $lesson->status,
                'description' => $lesson->description,
                'user_is_registered' => $userIsRegistered,
                'img_url' => $lesson->category->img_url ?? 'detail-cover.jfif', 
            ],
            'user' => $userProfile ? [
                'credits' => $userProfile->credits,
                'valid_date' => $userProfile->valid_date,
                'role' => $user->role,
            ] : null,
        ]);
    }
}
