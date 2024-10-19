<?php

namespace App\Http\Controllers;

use App\Models\LessonRegistration;
use Illuminate\Http\Request;
use App\Models\Lesson;

class RegistrationController extends Controller
{
    public function update(Request $request, $id)
    {
        $request->validate([
            'confirmation_status' => 'required|in:Confirmed,Pending,Canceled',
        ]);

        try {
            $registration = LessonRegistration::findOrFail($id);
            $user = $registration->user;
            $lesson = $registration->lesson;

            $errorMessage = $this->handleRegistrationUpdate($request->confirmation_status, $user, $lesson, $registration);

            if ($errorMessage) {
                return redirect()->back()->with('error', $errorMessage);
            }
            
            $registration->confirmation_status = $request->confirmation_status;
            $registration->save();

            $this->updateRegisteredStudentsCount($lesson);

            return redirect()->back()->with('success', 'Confirmation status updated successfully.' . ($lesson->payment_type === 'credits' ? ' Credits updated.' : ''));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the status: ' . $e->getMessage());
        }
    }
    // FUNCTION TO HANDLE CREDITS BASED ON STATUS
    private function handleRegistrationUpdate($confirmationStatus, $user, $lesson, $registration)
    {
        if ($lesson->payment_type === 'credits') {
            if ($confirmationStatus === 'Confirmed') {

                if ($user->profile->credits > 0) {
                    $user->profile->credits -= 1;
                    $user->profile->save();
                } else {
                    return '"Registration cannot be confirmed as the student currently has no available credits.';
                }
            } elseif ($confirmationStatus === 'Canceled' || $confirmationStatus === 'Pending') {
                if ($registration->confirmation_status === 'Confirmed') {
                    $user->profile->credits += 1;
                    $user->profile->save();
                }
            }
        }
        return null;
    }
    // FUNCTION TO HANDLE REGISTERED STUDENTS
    private function updateRegisteredStudentsCount($lesson)
    {
        $confirmedCount = LessonRegistration::where('lesson_id', $lesson->id)
            ->where('confirmation_status', 'Confirmed')
            ->count();

        $lesson->registered_students = $confirmedCount;
        $lesson->save();
    }


    // Lesson join
    public function register(Request $request, $lessonId)
    {
        $user = auth()->user();
        $lesson = Lesson::findOrFail($lessonId);

        $registration = LessonRegistration::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if (!$registration) {
            $registration = new LessonRegistration();
            $registration->user_id = $user->id;
            $registration->lesson_id = $lesson->id;
        }

        // Deduct one credit and set status to "Confirmed"
        if ($user->profile->credits > 0) {
            $user->profile->credits -= 1;
            $user->profile->save();

            $registration->confirmation_status = 'Confirmed';
            $registration->save();

            $lesson->registered_students += 1;
            $lesson->save();

            return redirect()->back()->with('success', 'You have successfully registered to join the class.');
        } else {
            return redirect()->back()->with('error', 'Not enough credits.');
        }
    }

    public function cancel(Request $request, $lessonId)
    {
        $user = auth()->user();
        $lesson = Lesson::findOrFail($lessonId);

        // Find the existing registration
        $registration = LessonRegistration::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($registration && $registration->confirmation_status === 'Confirmed') {
            // Add back one credit and set status to "Canceled"
            $user->profile->credits += 1;
            $user->profile->save();

            $registration->confirmation_status = 'Canceled';
            $registration->save();

            $lesson->registered_students -= 1;
            $lesson->save();

            return redirect()->back()->with('success', 'Your registration has been canceled.');
        }

        return redirect()->back()->with('error', 'Unable to cancel the registration.');
    }
}
