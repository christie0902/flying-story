<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ProfileController extends Controller
{
    use ValidatesRequests;
    public function loadProfile()
    {
        $profile = auth()->user()->profile;
        return view('profile.show', compact('profile'));
    }

    public function editForm()
    {
        $profile = auth()->user()->profile;
        return view('profile.edit', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()], //make sure the exclude the current user email when check
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email address is already in use. Please choose a different one.',
        ]);
        $profile = auth()->user()->profile;
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->phone = $request->phone;
        $profile->email = $request->email;
        $profile->save();


        $profile->save();

        return redirect()->route('profile.load')->with('success_message', 'Profile updated successfully');
    }
}
