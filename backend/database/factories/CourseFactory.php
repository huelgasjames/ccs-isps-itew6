<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courseCodes = ['CCS 101', 'CCS 102', 'CCS 103', 'CCS 104', 'CCS 105', 'CCS 106', 'CCS 107', 'CCS 108', 'CCS 109', 'CCS 110', 'CCS 111', 'CCS 112'];
        $courseNames = [
            'Introduction to Computer Science',
            'Programming Fundamentals',
            'Data Structures and Algorithms',
            'Web Development Fundamentals',
            'Database Management Systems',
            'Software Engineering',
            'Computer Networks',
            'Operating Systems',
            'Artificial Intelligence',
            'Machine Learning',
            'Cybersecurity Fundamentals',
            'Mobile Application Development'
        ];
        $departments = ['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Engineering', 'Business', 'Arts'];
        $semesters = ['First Semester', 'Second Semester', 'Summer'];
        $instructors = [
            'Dr. John Smith', 'Prof. Jane Doe', 'Dr. Robert Johnson', 'Prof. Emily Davis',
            'Dr. Michael Wilson', 'Prof. Sarah Brown', 'Dr. David Martinez', 'Prof. Lisa Anderson'
        ];
        
        $index = fake()->numberBetween(0, 11);
        
        return [
            'course_code' => $courseCodes[$index],
            'course_name' => $courseNames[$index],
            'description' => fake()->sentence(15),
            'credits' => fake()->numberBetween(2, 4),
            'department' => $departments[array_rand($departments)],
            'semester' => $semesters[array_rand($semesters)],
            'academic_year' => '2024-2025',
            'instructor' => $instructors[array_rand($instructors)],
            'schedule' => fake()->randomElement(['MWF 9:00-10:30 AM', 'TTH 1:00-2:30 PM', 'MWF 2:00-3:30 PM', 'TTH 9:00-10:30 AM']),
            'room' => 'Room ' . fake()->numberBetween(100, 999),
            'max_students' => fake()->numberBetween(25, 50),
            'current_students' => fake()->numberBetween(15, 45),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive']),
            'prerequisites' => $index > 0 ? [$courseCodes[$index - 1]] : [],
        ];
    }

    /**
     * Create a course with specific course code and name
     */
    public function createSpecificCourse(string $courseCode, string $courseName): static
    {
        return $this->state(fn (array $attributes) => [
            'course_code' => $courseCode,
            'course_name' => $courseName,
        ]);
    }

    /**
     * Create an active course
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create an inactive course
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a full course
     */
    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_students' => $attributes['max_students'] ?? 30,
        ]);
    }

    /**
     * Create a Computer Science course
     */
    public function computerScience(): static
    {
        return $this->state(fn (array $attributes) => [
            'department' => 'Computer Science',
        ]);
    }
}
