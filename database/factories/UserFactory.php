<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // Default password for all users
        ];
    }

    /**
     * Indicate that the user should have an associated profile.
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->profile()->create(Profile::factory()->make()->toArray());
        });
    }
}
