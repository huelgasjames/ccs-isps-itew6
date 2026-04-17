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
                'violation_type' => 'Late Submission',
                'description' => 'Submitted programming assignment 2 days late',
                'sanction' => 'Grade deduction of 10%',
                'date_committed' => '2024-01-15',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
            [
                'violation_type' => 'Attendance Issue',
                'description' => 'Absent for 3 consecutive classes without valid reason',
                'sanction' => 'Warning letter sent to parents',
                'date_committed' => '2024-02-10',
                'status' => 'pending',
                'severity' => 'moderate'
            ],
            [
                'violation_type' => 'Code of Conduct Violation',
                'description' => 'Use of mobile phone during examination',
                'sanction' => 'Automatic failure in exam',
                'date_committed' => '2024-01-25',
                'status' => 'resolved',
                'severity' => 'major'
            ],
            [
                'violation_type' => 'Plagiarism',
                'description' => 'Copied content from online sources without proper citation',
                'sanction' => 'Grade penalty and academic probation',
                'date_committed' => '2024-03-05',
                'status' => 'pending',
                'severity' => 'major'
            ],
            [
                'violation_type' => 'Dress Code Violation',
                'description' => 'Not wearing proper uniform on multiple occasions',
                'sanction' => 'Verbal warning',
                'date_committed' => '2024-01-10',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
            [
                'violation_type' => 'Disruptive Behavior',
                'description' => 'Causing disturbance during class hours',
                'sanction' => 'Parent conference required',
                'date_committed' => '2024-02-20',
                'status' => 'pending',
                'severity' => 'moderate'
            ],
            [
                'violation_type' => 'Late Arrival',
                'description' => 'Consistently arriving 15+ minutes late to class',
                'sanction' => 'Detention for 1 hour',
                'date_committed' => '2024-03-01',
                'status' => 'resolved',
                'severity' => 'minor'
            ],
            [
                'violation_type' => 'Unauthorized Device Use',
                'description' => 'Using laptop during non-computer class without permission',
                'sanction' => 'Device confiscated for the day',
                'date_committed' => '2024-02-15',
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
                        'violation_type' => $violation['violation_type'],
                        'description' => $violation['description'],
                        'sanction' => $violation['sanction'],
                        'date_committed' => $violation['date_committed'],
                        'status' => $violation['status'],
                        'severity' => $violation['severity'],
                        'reported_by' => 'Faculty Member',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
