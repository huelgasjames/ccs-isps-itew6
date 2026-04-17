<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentAffiliation;
use Illuminate\Database\Seeder;

class StudentAffiliationSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing students
        $students = Student::all();

        // Sample affiliations
        $affiliations = [
            [
                'organization_name' => 'Computer Science Society',
                'type' => 'Academic',
                'position' => 'Member',
                'start_date' => '2022-08-01',
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'organization_name' => 'Programming Club',
                'type' => 'Academic',
                'position' => 'Vice President',
                'start_date' => '2023-06-01',
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'organization_name' => 'Basketball Varsity Team',
                'type' => 'Sports',
                'position' => 'Team Captain',
                'start_date' => '2022-06-01',
                'end_date' => '2024-03-31',
                'status' => 'completed'
            ],
            [
                'organization_name' => 'Student Council',
                'type' => 'Leadership',
                'position' => 'Secretary',
                'start_date' => '2023-06-01',
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'organization_name' => 'Web Development Guild',
                'type' => 'Academic',
                'position' => 'Member',
                'start_date' => '2022-09-01',
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'organization_name' => 'Environmental Advocacy Group',
                'type' => 'Advocacy',
                'position' => 'Volunteer',
                'start_date' => '2023-01-15',
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'organization_name' => 'Debate Society',
                'type' => 'Academic',
                'position' => 'Member',
                'start_date' => '2022-10-01',
                'end_date' => '2024-02-28',
                'status' => 'completed'
            ],
            [
                'organization_name' => 'Music Club',
                'type' => 'Cultural',
                'position' => 'Guitarist',
                'start_date' => '2022-08-15',
                'end_date' => null,
                'status' => 'active'
            ],
        ];

        foreach ($students as $student) {
            // Assign 2-3 random affiliations to each student
            $studentAffiliations = collect($affiliations)->random(rand(2, 3));
            
            foreach ($studentAffiliations as $affiliation) {
                StudentAffiliation::create([
                    'student_id' => $student->id,
                    'organization_name' => $affiliation['organization_name'],
                    'type' => $affiliation['type'],
                    'position' => $affiliation['position'],
                    'start_date' => $affiliation['start_date'],
                    'end_date' => $affiliation['end_date'],
                    'status' => $affiliation['status'],
                    'description' => 'Active member of ' . $affiliation['organization_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
