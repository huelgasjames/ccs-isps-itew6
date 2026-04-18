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
                'name' => 'Computer Science Society',
                'type' => 'student_organization',
                'role' => 'Member',
                'position' => 'Member',
                'start_date' => '2022-08-01',
                'description' => 'Active member of Computer Science Society'
            ],
            [
                'name' => 'Programming Club',
                'type' => 'student_organization',
                'role' => 'Vice President',
                'position' => 'Vice President',
                'start_date' => '2023-06-01',
                'description' => 'Vice President of Programming Club'
            ],
            [
                'name' => 'Basketball Varsity Team',
                'type' => 'professional',
                'role' => 'Team Captain',
                'position' => 'Team Captain',
                'start_date' => '2022-06-01',
                'description' => 'Team Captain of Basketball Varsity'
            ],
            [
                'name' => 'Student Council',
                'type' => 'student_organization',
                'role' => 'Secretary',
                'position' => 'Secretary',
                'start_date' => '2023-06-01',
                'description' => 'Secretary of Student Council'
            ],
            [
                'name' => 'Web Development Guild',
                'type' => 'student_organization',
                'role' => 'Member',
                'position' => 'Member',
                'start_date' => '2022-09-01',
                'description' => 'Member of Web Development Guild'
            ],
            [
                'name' => 'Environmental Advocacy Group',
                'type' => 'community',
                'role' => 'Volunteer',
                'position' => 'Volunteer',
                'start_date' => '2023-01-15',
                'description' => 'Volunteer for Environmental Advocacy'
            ],
            [
                'name' => 'Debate Society',
                'type' => 'student_organization',
                'role' => 'Member',
                'position' => 'Member',
                'start_date' => '2022-10-01',
                'description' => 'Member of Debate Society'
            ],
            [
                'name' => 'Music Club',
                'type' => 'student_organization',
                'role' => 'Guitarist',
                'position' => 'Guitarist',
                'start_date' => '2022-08-15',
                'description' => 'Guitarist in Music Club'
            ],
        ];

        foreach ($students as $student) {
            // Assign 2-3 random affiliations to each student
            $studentAffiliations = collect($affiliations)->random(rand(2, 3));
            
            foreach ($studentAffiliations as $affiliation) {
                StudentAffiliation::create([
                    'student_id' => $student->id,
                    'name' => $affiliation['name'],
                    'type' => $affiliation['type'],
                    'role' => $affiliation['role'],
                    'position' => $affiliation['position'],
                    'start_date' => $affiliation['start_date'],
                    'description' => $affiliation['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
