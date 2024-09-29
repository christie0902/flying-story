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
 
    // public function updateCredits(Request $request, $id)
    // {
    //     $request->validate([
    //         'credits' => 'required|integer|min:0'
    //     ]);

    //     $user = User::findOrFail($id);

    //     $profile = $user->profile;

    //     if ($profile) {
    //         $profile->credits = $request->input('credits');
    //         $profile->save();

    //         return response()->json(['success' => true]);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
    // }
    public function updateCredits(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'credits' => 'required|integer|min:0',
        ]);
    
        // Find the user by ID
        $user = User::findOrFail($id);
        $profile = $user->profile;
    
        if ($profile) {
            // Update profile credits
            $profile->credits = $request->input('credits');
            $profile->save(); // Save the updated credits
    
            // Redirect back with success message
            return redirect()->back()->with('success', "{$user->name}'s credits have been updated to {$profile->credits}.");
        }
    
        // If no profile is found, return an error
        return redirect()->back()->with('error', 'Profile not found.');
    }
}
