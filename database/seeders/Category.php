<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categories;

class Category extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['cat_name' => 'Web Developer'],
            ['cat_name' => 'Graphic Designer'],
            ['cat_name' => 'App Developer'],
        ];

        foreach ($categories as $category) {
            Categories::create($category);
        }

           }
}
