<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\Talent;
use App\Models\Sport;
use App\Models\Certificate;
use App\Models\Organization;
use App\Models\StudentViolation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StudentBasicDataSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        
        if ($students->count() < 2) {
            $this->command->error('Students not found. Please run StudentSeeder first.');
            return;
        }

        $student1 = $students->first();
        $student2 = $students->skip(1)->first();

        // Create Skills for Student 1
        StudentSkill::create([
            'student_id' => $student1->id,
            'name' => 'Web Development',
            'category' => 'technical',
            'proficiency' => 'advanced',
            'years_experience' => 2,
            'certifications' => 'Web Development Certification',
            'last_used' => now()->subMonths(3),
        ]);

        StudentSkill::create([
            'student_id' => $student1->id,
            'name' => 'Graphic Design',
            'category' => 'creative',
            'proficiency' => 'intermediate',
            'years_experience' => 1,
            'certifications' => null,
            'last_used' => now()->subMonths(6),
        ]);

        // Create Skills for Student 2
        StudentSkill::create([
            'student_id' => $student2->id,
            'name' => 'Networking',
            'category' => 'technical',
            'proficiency' => 'advanced',
            'years_experience' => 2,
            'certifications' => 'CCNA Certification',
            'last_used' => now()->subMonths(2),
        ]);

        StudentSkill::create([
            'student_id' => $student2->id,
            'name' => 'Database Management',
            'category' => 'technical',
            'proficiency' => 'intermediate',
            'years_experience' => 1,
            'certifications' => null,
            'last_used' => now()->subMonths(4),
        ]);

        // Create Talents
        Talent::create([
            'student_id' => $student1->id,
            'talent_name' => 'Singing',
        ]);

        Talent::create([
            'student_id' => $student2->id,
            'talent_name' => 'Painting',
        ]);

        // Create Sports
        Sport::create([
            'student_id' => $student1->id,
            'sport_name' => 'Basketball',
        ]);

        Sport::create([
            'student_id' => $student2->id,
            'sport_name' => 'Chess',
        ]);

        // Create Certificates
        Certificate::create([
            'student_id' => $student1->id,
            'certificate_name' => 'Web Development Certification',
            'date_received' => '2023-05-15',
        ]);

        Certificate::create([
            'student_id' => $student2->id,
            'certificate_name' => 'Network Security Certification',
            'date_received' => '2023-08-20',
        ]);

        // Create Organizations
        Organization::create([
            'student_id' => $student1->id,
            'name' => 'Computer Science Society',
            'type' => 'Student',
        ]);

        Organization::create([
            'student_id' => $student2->id,
            'name' => 'Programming Club',
            'type' => 'Student',
        ]);

        // Create Violations (some pending to test at-risk functionality)
        StudentViolation::create([
            'student_id' => $student2->id,
            'type' => 'academic',
            'description' => 'Submitted project 3 days late',
            'date' => '2024-01-15',
            'status' => 'pending',
            'severity' => 'minor',
            'consequence' => 'Warning Letter',
        ]);

        StudentViolation::create([
            'student_id' => $student2->id,
            'type' => 'attendance',
            'description' => 'Absent for 5 consecutive classes',
            'date' => '2024-01-20',
            'status' => 'pending',
            'severity' => 'moderate',
            'consequence' => 'Parent Notification',
        ]);

        StudentViolation::create([
            'student_id' => $student2->id,
            'type' => 'academic',
            'description' => 'Plagiarism in assignment',
            'date' => '2024-01-25',
            'status' => 'pending',
            'severity' => 'major',
            'consequence' => 'Grade Penalty',
        ]);

        StudentViolation::create([
            'student_id' => $student1->id,
            'type' => 'conduct',
            'description' => 'Late for class',
            'date' => '2024-01-10',
            'status' => 'resolved',
            'severity' => 'minor',
            'consequence' => 'Verbal Warning',
        ]);

        $this->command->info('Student basic data seeded: skills, talents, sports, certificates, organizations, violations');
    }
}
