<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;

class LessonRecurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Lesson::factory()->count(10)->create();
    }
}
