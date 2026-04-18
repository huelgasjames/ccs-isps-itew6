<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentViolation;
use Illuminate\Database\Seeder;

class StudentViolationSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing students
        $students = Student::all();

        // Sample violations
        $violations = [
            [
                'type' => 'academic',
                'description' => 'Submitted programming assignment 2 days late',
                'consequence' => 'Grade deduction of 10%',
                'date' => '2024-01-15',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
            [
                'type' => 'attendance',
                'description' => 'Absent for 3 consecutive classes without valid reason',
                'consequence' => 'Warning letter sent to parents',
                'date' => '2024-02-10',
                'status' => 'pending',
                'severity' => 'moderate'
            ],
            [
                'type' => 'disciplinary',
                'description' => 'Use of mobile phone during examination',
                'consequence' => 'Automatic failure in exam',
                'date' => '2024-01-25',
                'status' => 'resolved',
                'severity' => 'major'
            ],
            [
                'type' => 'academic',
                'description' => 'Copied content from online sources without proper citation',
                'consequence' => 'Grade penalty and academic probation',
                'date' => '2024-03-05',
                'status' => 'pending',
                'severity' => 'major'
            ],
            [
                'type' => 'conduct',
                'description' => 'Not wearing proper uniform on multiple occasions',
                'consequence' => 'Verbal warning',
                'date' => '2024-01-10',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
            [
                'type' => 'disciplinary',
                'description' => 'Causing disturbance during class hours',
                'consequence' => 'Parent conference required',
                'date' => '2024-02-20',
                'status' => 'pending',
                'severity' => 'moderate'
            ],
            [
                'type' => 'attendance',
                'description' => 'Consistently arriving 15+ minutes late to class',
                'consequence' => 'Detention for 1 hour',
                'date' => '2024-03-01',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
            [
                'type' => 'disciplinary',
                'description' => 'Using laptop during non-computer class without permission',
                'consequence' => 'Device confiscated for the day',
                'date' => '2024-02-15',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
        ];

        foreach ($students as $student) {
            // Assign 1-3 random violations to each student (some students may have none)
            if (rand(0, 1)) { // 50% chance of having violations
                $violationCount = rand(1, 3);
                $studentViolations = collect($violations)->random($violationCount);
                
                foreach ($studentViolations as $violation) {
                    StudentViolation::create([
                        'student_id' => $student->id,
                        'type' => $violation['type'],
                        'description' => $violation['description'],
                        'consequence' => $violation['consequence'],
                        'date' => $violation['date'],
                        'status' => $violation['status'],
                        'severity' => $violation['severity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
