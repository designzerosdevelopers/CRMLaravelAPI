<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Degree;

class Degrees extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    { 

        $degrees = [
            ['degree_title' => 'Bachelor\'s in Science'],
            ['degree_title' => 'Bachelor\'s in Chemical Engineering'],
            ['degree_title' => 'Bachelor\'s in Mechanical Engineering'],
            ['degree_title' => 'Bachelor\'s in IT or Computer Science'],
            ['degree_title' => 'Bachelor\'s in Art'],
            ['degree_title' => 'Bachelor\'s in Electrical Engineering'],
            ['degree_title' => 'Master\'s in IT or Computer Science'],
            ['degree_title' => 'Master\'s in Art'],
            ['degree_title' => 'Master\'s in Chemical Engineering'],
            ['degree_title' => 'Master\'s in Electrical Engineering'],
            ['degree_title' => 'Master\'s in Mechanical Engineering'],
            ['degree_title' => 'Master\'s in Software Engineering'],
            ['degree_title' => 'PhD in Machine Learning'],
            ['degree_title' => 'PhD in Artificial Intelligence'],
            ['degree_title' => 'PhD in IT or Computer Science'],
            ['degree_title' => 'PhD in Arts'],
            ['degree_title' => 'PhD in Engineering Management'],
        ];
        

        foreach ($degrees as $degree) {
            Degree::create($degree);
        }
           }
}
