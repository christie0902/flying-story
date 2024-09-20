<?php

namespace Database\Factories;

use App\Models\LessonRegistration;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonRegistrationFactory extends Factory
{
    protected $model = LessonRegistration::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, 
            'lesson_id' => Lesson::inRandomOrder()->first()->id,
            'registration_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid', 'canceled']),
        ];
    }
}

