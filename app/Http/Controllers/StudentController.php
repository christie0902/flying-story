<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
}
