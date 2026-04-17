<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts of computer science and programming',
                'credits' => 3,
                'year_level' => 1,
                'semester' => 'first',
                'department' => 'CCS',
                'prerequisites' => null,
            ],
            [
                'course_code' => 'CS102',
                'course_name' => 'Computer Programming 1',
                'description' => 'Introduction to programming using Python',
                'credits' => 4,
                'year_level' => 1,
                'semester' => 'second',
                'department' => 'CCS',
                'prerequisites' => 'CS101',
            ],
            [
                'course_code' => 'CS201',
                'course_name' => 'Data Structures and Algorithms',
                'description' => 'Fundamental data structures and algorithm analysis',
                'credits' => 4,
                'year_level' => 2,
                'semester' => 'first',
                'department' => 'CCS',
                'prerequisites' => 'CS102',
            ],
            [
                'course_code' => 'CS202',
                'course_name' => 'Web Development',
                'description' => 'Front-end and back-end web development',
                'credits' => 3,
                'year_level' => 2,
                'semester' => 'second',
                'department' => 'CCS',
                'prerequisites' => 'CS102',
            ],
            [
                'course_code' => 'CS301',
                'course_name' => 'Database Management Systems',
                'description' => 'Database design, implementation, and management',
                'credits' => 3,
                'year_level' => 3,
                'semester' => 'first',
                'department' => 'CCS',
                'prerequisites' => 'CS201',
            ],
            [
                'course_code' => 'CS302',
                'course_name' => 'Software Engineering',
                'description' => 'Software development methodologies and project management',
                'credits' => 3,
                'year_level' => 3,
                'semester' => 'second',
                'department' => 'CCS',
                'prerequisites' => 'CS202',
            ],
            [
                'course_code' => 'CS401',
                'course_name' => 'Machine Learning',
                'description' => 'Introduction to machine learning algorithms and applications',
                'credits' => 3,
                'year_level' => 4,
                'semester' => 'first',
                'department' => 'CCS',
                'prerequisites' => 'CS301',
            ],
            [
                'course_code' => 'CS402',
                'course_name' => 'Capstone Project',
                'description' => 'Final year project integrating all learned concepts',
                'credits' => 6,
                'year_level' => 4,
                'semester' => 'second',
                'department' => 'CCS',
                'prerequisites' => 'CS302',
            ],
            [
                'course_code' => 'IT101',
                'course_name' => 'Information Technology Fundamentals',
                'description' => 'Basic concepts in information technology',
                'credits' => 3,
                'year_level' => 1,
                'semester' => 'first',
                'department' => 'CCS',
                'prerequisites' => null,
            ],
            [
                'course_code' => 'IT201',
                'course_name' => 'Network Administration',
                'description' => 'Network setup, configuration, and administration',
                'credits' => 4,
                'year_level' => 2,
                'semester' => 'second',
                'department' => 'CCS',
                'prerequisites' => 'IT101',
            ],
        ];

        foreach ($courses as $course) {
            Course::create([
                'course_code' => $course['course_code'],
                'course_name' => $course['course_name'],
                'description' => $course['description'],
                'credits' => $course['credits'],
                'year_level' => $course['year_level'],
                'semester' => $course['semester'],
                'department' => $course['department'],
                'prerequisites' => $course['prerequisites'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
