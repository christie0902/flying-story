<?php
namespace App\Http\Controllers;

use App\Models\LessonRegistration;
use Illuminate\Http\Request;

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

        if ($request->confirmation_status === 'Confirmed') {
            if ($user->profile->credits > 0) {
                $user->profile->credits -= 1;
                $user->profile->save();
            } else {
                return redirect()->back()->with('error', 'User has no credits to confirm the registration.');
            }
        } elseif ($request->confirmation_status === 'Canceled') {
            if ($registration->confirmation_status === 'Confirmed') {
                $user->profile->credits += 1;
                $user->profile->save();
            }
        } elseif ($request->confirmation_status === 'Pending') {
            if ($registration->confirmation_status === 'Confirmed') {
                $user->profile->credits += 1;
                $user->profile->save();
            }
        }

        $registration->confirmation_status = $request->confirmation_status;
        $registration->save();

        return redirect()->back()->with('success', 'Confirmation status updated successfully. Credits updated');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'An error occurred while updating the status: ' . $e->getMessage());
    }
}
}
