<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Professor;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Get courses and professors
        $courses = Course::all();
        $professors = Professor::all();

        if ($courses->isEmpty() || $professors->isEmpty()) {
            $this->command->error('Courses or Professors not found. Please run CourseSeeder and ProfessorSeeder first.');
            return;
        }

        $timeSlots = [
            ['start' => '07:30', 'end' => '09:00'],
            ['start' => '09:30', 'end' => '11:00'],
            ['start' => '11:30', 'end' => '13:00'],
            ['start' => '13:30', 'end' => '15:00'],
            ['start' => '15:30', 'end' => '17:00'],
            ['start' => '17:30', 'end' => '19:00'],
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $rooms = [
            ['Room 101', 'lab'],
            ['Room 102', 'lecture'],
            ['Room 103', 'computer_lab'],
            ['Room 201', 'lecture'],
            ['Room 202', 'lab'],
            ['Room 203', 'computer_lab'],
            ['Room 301', 'lecture'],
            ['Room 302', 'multimedia'],
        ];

        $sections = ['BSIT-1A', 'BSIT-1B', 'BSCS-1A', 'BSCS-1B', 'BSIT-2A', 'BSIT-2B', 'BSCS-2A', 'BSCS-2B'];

        // Create schedules for each course
        foreach ($courses as $index => $course) {
            $professor = $professors[$index % $professors->count()];
            
            // Create 2-3 schedules per course (different sections)
            $numSchedules = rand(2, 3);
            
            for ($i = 0; $i < $numSchedules; $i++) {
                $timeSlot = $timeSlots[$index % count($timeSlots)];
                $day = $days[($index * $i) % count($days)];
                $room = $rooms[($index + $i) % count($rooms)];
                $section = $sections[($index + $i) % count($sections)];
                
                Schedule::create([
                    'course_id' => $course->id,
                    'professor_id' => $professor->id,
                    'section' => $section,
                    'room' => $room[0],
                    'room_type' => $room[1],
                    'day_of_week' => $day,
                    'start_time' => $timeSlot['start'],
                    'end_time' => $timeSlot['end'],
                    'academic_year' => '2024-2025',
                    'semester' => $course->semester,
                    'max_capacity' => 40,
                    'current_enrollment' => rand(25, 38),
                    'status' => 'active',
                    'notes' => 'Regular class schedule. Please bring required materials.',
                ]);
            }
        }

        $this->command->info('Schedules seeded successfully!');
    }
}
