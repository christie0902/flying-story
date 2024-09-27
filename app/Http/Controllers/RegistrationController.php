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
            $registration->confirmation_status = $request->confirmation_status;
            $registration->save();

            return redirect()->back()->with('success', 'Confirmation status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the status: ' . $e->getMessage());
        }
    }
}
