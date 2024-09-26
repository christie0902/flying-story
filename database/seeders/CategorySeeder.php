<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Aerial Sling',
            'Aerial Hoop',
            'Aerial Silk',
            'Workshop',
        ];

        // Insert the categories into the database
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
