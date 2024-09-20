<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,  
            'credits_purchased' => $this->faker->numberBetween(5, 8),
            'payment_status' => $this->faker->randomElement(['pending', 'completed']),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

