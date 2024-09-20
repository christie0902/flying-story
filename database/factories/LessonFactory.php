<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Recurrence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        $categories = ['Aerial Yoga', 'Aerial Sling', 'Aerial Hoop'];
        $levels = ['beginner', 'lower-intermediate', 'intermediate', 'upper-intermediate', 'advanced'];

        return [
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(10),
            'schedule' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'capacity' => $this->faker->numberBetween(10, 20),
            'registered_students' => $this->faker->numberBetween(0, 10),
            'status' => $this->faker->randomElement(['active', 'canceled']),
            'level' => $this->faker->randomElement($levels),
            'recurrence_id' => Recurrence::factory(),
        ];
    }
}
