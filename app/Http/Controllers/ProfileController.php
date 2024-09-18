<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function loadProfile()
    {
        $profile = auth()->user()->profile;
        return view('profile.show', compact('profile'));
    }
}
