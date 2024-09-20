<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'credits' => $this->faker->numberBetween(0, 8),
            'credits_purchased_date' => $this->faker->date(),
            'valid_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'payment_variable' => $this->faker->word(),
        ];
    }
}
