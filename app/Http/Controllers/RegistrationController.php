<?php

namespace App\Http\Controllers;

use App\Models\LessonRegistration;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

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
        if ($lesson->payment_type !== 'credits') {
            return null;
        }
    
        $cost = (int) ($lesson->credits_cost ?? 1);
    
        if ($confirmationStatus === 'Confirmed' && $registration->confirmation_status !== 'Confirmed') {
            $user->profile->credits -= $cost;
            $user->profile->save();
            return null;
        }
    
        // If moving AWAY from Confirmed => refund (but only if it WAS confirmed)
        if ($confirmationStatus === "Canceled" && $registration->confirmation_status === 'Confirmed') {
            $user->profile->credits += $cost;
            $user->profile->save();
            return null;
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
    
        return DB::transaction(function () use ($user, $lessonId) {
            $lesson = Lesson::lockForUpdate()->findOrFail($lessonId);
    
            // Lock the profile row too so credits can't be deducted twice concurrently
            $profile = $user->profile()->lockForUpdate()->firstOrFail();
    
            $registration = LessonRegistration::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->lockForUpdate()
                ->first();
    
            if (!$registration) {
                $registration = new LessonRegistration([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                ]);
            }
    
            // already confirmed => don't double deduct
            if ($registration->exists && $registration->confirmation_status === 'Confirmed') {
                return redirect()->back()->with('success', 'You are already registered for this class.');
            }
    
            $cost = (int) ($lesson->credits_cost ?? 1);
    
            if ($lesson->payment_type === 'credits') {
                $profile->credits -= $cost;
                $profile->save();
            }
    
            $registration->confirmation_status = 'Confirmed';
            $registration->save();
    
            $this->updateRegisteredStudentsCount($lesson);
    
            return redirect()->back()->with('success', 'You have successfully registered to join the class.');
        });
    }
    

        public function cancel(Request $request, $lessonId)
    {
        $user = auth()->user();

        return DB::transaction(function () use ($user, $lessonId) {
            $lesson = Lesson::lockForUpdate()->findOrFail($lessonId);
            $profile = $user->profile()->lockForUpdate()->firstOrFail();

            $registration = LessonRegistration::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->lockForUpdate()
                ->first();

            if (!$registration || $registration->confirmation_status !== 'Confirmed') {
                return redirect()->back()->with('error', 'Unable to cancel the registration.');
            }

            $cost = (int) ($lesson->credits_cost ?? 1);

            if ($lesson->payment_type === 'credits') {
                $profile->credits += $cost;
                $profile->save();
            }

            $registration->confirmation_status = 'Canceled';
            $registration->save();

            $this->updateRegisteredStudentsCount($lesson);

            return redirect()->back()->with('success', 'Your registration has been canceled.');
        });
    }

}
