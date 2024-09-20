<?php

namespace Database\Factories;

use App\Models\Recurrence;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurrenceFactory extends Factory
{
    protected $model = Recurrence::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'frequency' => $this->faker->randomElement(['weekly', 'bi-weekly', 'monthly']),
            'interval' => $this->faker->numberBetween(1, 4),
            'days_of_week' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+6 months'),
        ];
    }
}
