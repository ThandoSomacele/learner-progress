<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            'Accounting',
            'Afrikaans',
            'Business Studies',
            'Computer Applications Technology',
            'Dramatic Arts',
            'Economics',
            'Electrical Technology',
            'English',
            'Geography',
            'History',
            'Hospitality Studies',
            'Information Technology',
            'IsiNdebele',
            'IsiXhosa',
            'IsiZulu',
            'Life Orientation',
            'Life Sciences',
            'Mathematical Literacy',
            'Mathematics',
            'Music',
            'Physical Sciences',
            'Sepedi',
            'Sesotho',
            'Setswana',
            'Siswati',
            'Tourism',
            'Tshivenda',
            'Visual Arts',
            'Xitsonga'
        ];

        foreach ($courses as $course) {
            Course::query()->insert([
                'name' => $course,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
