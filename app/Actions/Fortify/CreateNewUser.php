<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Log;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'credits' => ['nullable', 'integer'],
        ])->validate();

        // Create the user
        $user = User::create([
            'name' => $input['first_name'] . ' ' . $input['last_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => 'student',
        ]);

        $paymentVariable = $this->generateUniquePaymentVariable();

        // Create the profile for the user
        Profile::create([
            'user_id' => $user->id,
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'phone' => $input['phone'] ?? '',
            'email' => $input['email'],
            'credits' => 0,
            'payment_variable' => $paymentVariable,
        ]);

        return $user;
    }

    private function generateUniquePaymentVariable()
    {
        do {
            $paymentVariable = rand(10000, 99999);

            $exists = Profile::where('payment_variable', $paymentVariable)->exists();

            // dd("Generated payment variable: $paymentVariable, Exists: $exists");
        } while ($exists);

        return $paymentVariable;
    }
}