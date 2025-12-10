<?php

namespace Database\Seeders;

use App\Models\Enrolment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LearnersTableSeeder::class,
            CoursesTableSeeder::class,
            EnrolmentsTableSeeder::class,
        ]);
    }
}
