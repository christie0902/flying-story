<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LessonRegistration;

class LessonRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        LessonRegistration::factory()->count(20)->create();
    }
}

