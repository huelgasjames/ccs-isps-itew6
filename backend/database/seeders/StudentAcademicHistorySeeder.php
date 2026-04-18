<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentAcademicHistory;
use Illuminate\Database\Seeder;

class StudentAcademicHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Get existing students
        $students = Student::all();

        // Sample academic history entries
        $academicHistories = [
            [
                'school_name' => 'Manila Science High School',
                'degree' => 'High School Diploma',
                'major' => 'Science and Technology',
                'start_date' => '2018-06-01',
                'end_date' => '2022-03-31',
                'gpa' => 3.8,
                'status' => 'completed',
                'honors' => ['With Honors', 'Best in Mathematics']
            ],
            [
                'school_name' => 'University of the Philippines',
                'degree' => 'Bachelor of Science in Computer Science',
                'major' => 'Software Engineering',
                'start_date' => '2022-06-01',
                'end_date' => '2026-03-31',
                'gpa' => 3.5,
                'status' => 'ongoing',
                'honors' => ['Dean\'s List']
            ],
            [
                'school_name' => 'Ateneo de Manila High School',
                'degree' => 'High School Diploma',
                'major' => 'General Academic',
                'start_date' => '2019-06-01',
                'end_date' => '2023-03-31',
                'gpa' => 3.6,
                'status' => 'completed',
                'honors' => ['With Honors']
            ],
            [
                'school_name' => 'De La Salle University',
                'degree' => 'Bachelor of Science in Information Technology',
                'major' => 'Web Development',
                'start_date' => '2023-06-01',
                'end_date' => '2027-03-31',
                'gpa' => 3.2,
                'status' => 'ongoing',
                'honors' => null
            ],
        ];

        foreach ($students as $index => $student) {
            // Assign 1-2 academic history entries to each student
            $historyCount = rand(1, 2);
            $selectedHistories = collect($academicHistories)->random($historyCount);
            
            foreach ($selectedHistories as $history) {
                StudentAcademicHistory::create([
                    'student_id' => $student->id,
                    'school_name' => $history['school_name'],
                    'degree' => $history['degree'],
                    'major' => $history['major'],
                    'start_date' => $history['start_date'],
                    'end_date' => $history['end_date'],
                    'gpa' => $history['gpa'],
                    'status' => $history['status'],
                    'honors' => $history['honors'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
