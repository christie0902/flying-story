<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Profile;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        $query = User::with('profile');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('profile', function ($query) use ($search) {
                        $query->where('payment_variable', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        // Apply filter for students with credits = 0 or about to expire
        if ($filter) {
            switch ($filter) {
                case 'zero_credits':
                    $query->whereHas('profile', function ($q) {
                        $q->where('credits', 0);
                    });
                    break;
                case 'expiring_credits':
                    $twoWeeksFromNow = Carbon::now()->addWeeks(2);
                    $query->whereHas('profile', function ($q) use ($twoWeeksFromNow) {
                        $q->where('valid_date', '<=', $twoWeeksFromNow);
                    });
                    break;
            }
        }
        $students = $query->paginate(10);

        return view('students.list', compact('students', 'search', 'filter'));
    }

    public function updateCredits(Request $request, $id)
    {
        $request->validate([
            'credits' => 'required|integer|min:0',
            'credits_purchased_date' => 'required|date',
            'valid_date' => 'required|date|after_or_equal:credits_purchased_date',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->profile;

        if ($profile) {
            $profile->credits = $request->input('credits');
            $profile->credits_purchased_date = $request->input('credits_purchased_date');
            $profile->valid_date = $request->input('valid_date');
            $profile->save();

            // Redirect back with a success message
            return redirect()->back()->with('success', "{$user->name}'s credits has been updated to {$profile->credits}.");
        }

        // If no profile is found, return an error
        return redirect()->back()->with('error', 'Profile not found.');
    }

    public function extendValidDate(Request $request, $id)
    {
        $request->validate([
            'valid_date' => 'required|date|after_or_equal:today',
        ]);

        $user = User::findOrFail($id);
        $profile = $user->profile;

        if ($profile) {
            $profile->valid_date = $request->input('valid_date');
            $profile->save();

            return redirect()->back()->with('success', "Valid date for {$user->name} has been extended to {$profile->valid_date}.");
        }

        return redirect()->back()->with('error', 'Profile not found.');
    }

    public function destroy($id)
    {
        $student = User::findOrFail($id);

        if (auth()->check() && auth()->user()->role === 'admin') {
            if ($student->profile) {
                $student->profile()->delete();
            }

            $student->delete();

            return redirect()->route('students.index')->with('success', 'Student account deleted successfully.');
        }

        return redirect()->route('students.index')->with('error', 'You do not have permission to delete this account.');
    }
}
