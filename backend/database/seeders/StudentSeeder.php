<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentAffiliation;
use App\Models\StudentSkill;
use App\Models\StudentViolation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Create Student 1 - Maria Santos (Good standing)
        $studentUser1 = User::withTrashed()->firstOrCreate([
            'email' => 'maria.student@ccs.edu',
        ], [
            'name' => 'Maria Santos',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
        if ($studentUser1->trashed()) {
            $studentUser1->restore();
        }

        $student1 = Student::withTrashed()->firstOrCreate([
            'student_id' => '2024-001',
        ], [
            'user_id' => $studentUser1->id,
            'first_name' => 'Maria',
            'middle_name' => 'Reyes',
            'last_name' => 'Santos',
            'email' => 'maria.student@ccs.edu',
            'phone' => '+639987654321',
            'date_of_birth' => '2004-05-15',
            'age' => 20,
            'gender' => 'female',
            'address' => '456 Avenue, City',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'postal_code' => '1000',
            'emergency_contact_name' => 'Ana Santos',
            'emergency_contact_relationship' => 'Mother',
            'emergency_contact_phone' => '+639123456780',
            'current_year' => 3,
            'current_semester' => 'second',
            'current_gpa' => 3.5,
            'total_units' => 120,
            'standing' => 'good',
            'advisor' => 'Prof. Juan Dela Cruz',
            // Legacy fields for compatibility
            'blood_type' => 'A+',
            'disability_status' => false,
            'scholar' => true,
            'working_student' => false,
            'contact_number' => '+639987654321',
            'section' => 'BSIT-3A',
            'year_level' => '3rd',
            'college' => 'CCS',
            'program' => 'BSIT',
            'curriculum' => '2020-2024',
            'academic_status' => 'Regular',
            'is_active' => true,
        ]);
        if ($student1->trashed()) {
            $student1->restore();
        }

        // Create Student 2 - Jose Reyes (At risk)
        $studentUser2 = User::withTrashed()->firstOrCreate([
            'email' => 'jose.student@ccs.edu',
        ], [
            'name' => 'Jose Reyes',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
        if ($studentUser2->trashed()) {
            $studentUser2->restore();
        }

        $student2 = Student::withTrashed()->firstOrCreate([
            'student_id' => '2024-002',
        ], [
            'user_id' => $studentUser2->id,
            'first_name' => 'Jose',
            'middle_name' => 'Lopez',
            'last_name' => 'Reyes',
            'email' => 'jose.student@ccs.edu',
            'phone' => '+639123456788',
            'date_of_birth' => '2003-08-20',
            'age' => 21,
            'gender' => 'male',
            'address' => '789 Boulevard, City',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
            'emergency_contact_name' => 'Pedro Reyes',
            'emergency_contact_relationship' => 'Father',
            'emergency_contact_phone' => '+639876543210',
            'current_year' => 2,
            'current_semester' => 'first',
            'current_gpa' => 2.8,
            'total_units' => 80,
            'standing' => 'average',
            'advisor' => 'Prof. Juan Dela Cruz',
            // Legacy fields for compatibility
            'blood_type' => 'B+',
            'disability_status' => false,
            'scholar' => false,
            'working_student' => true,
            'contact_number' => '+639123456788',
            'section' => 'BSCS-2B',
            'year_level' => '2nd',
            'college' => 'CCS',
            'program' => 'BSCS',
            'curriculum' => '2020-2024',
            'academic_status' => 'Regular',
            'is_active' => true,
        ]);
        if ($student2->trashed()) {
            $student2->restore();
        }

        // Add Skills for Maria Santos
        StudentSkill::updateOrCreate([
            'student_id' => $student1->id,
            'name' => 'Web Development',
        ], [
            'category' => 'technical',
            'proficiency' => 'advanced',
            'years_experience' => 3,
            'certifications' => 'HTML, CSS, JavaScript, React',
            'last_used' => '2024-03-15',
        ]);

        StudentSkill::updateOrCreate([
            'student_id' => $student1->id,
            'name' => 'Public Speaking',
        ], [
            'category' => 'soft',
            'proficiency' => 'intermediate',
            'years_experience' => 2,
            'certifications' => 'Leadership Training 2023',
            'last_used' => '2024-04-10',
        ]);

        StudentSkill::create([
            'student_id' => $student1->id,
            'name' => 'Graphic Design',
            'category' => 'creative',
            'proficiency' => 'beginner',
            'years_experience' => 1,
            'certifications' => null,
            'last_used' => '2024-02-20',
        ]);

        // Add Affiliations for Maria Santos
        StudentAffiliation::create([
            'student_id' => $student1->id,
            'name' => 'CCS Student Council',
            'type' => 'student_organization',
            'role' => 'Member',
            'start_date' => '2023-06-01',
            'position' => 'Events Coordinator',
            'description' => 'Organizing college events and activities',
        ]);

        StudentAffiliation::create([
            'student_id' => $student1->id,
            'name' => 'Web Development Club',
            'type' => 'student_organization',
            'role' => 'Member',
            'start_date' => '2022-08-15',
            'position' => 'Technical Lead',
            'description' => 'Leading web development projects and workshops',
        ]);

        // Add Skills for Jose Reyes
        StudentSkill::create([
            'student_id' => $student2->id,
            'name' => 'Python Programming',
            'category' => 'technical',
            'proficiency' => 'intermediate',
            'years_experience' => 2,
            'certifications' => 'Python Fundamentals',
            'last_used' => '2024-03-20',
        ]);

        StudentSkill::create([
            'student_id' => $student2->id,
            'name' => 'Basketball',
            'category' => 'sports',
            'proficiency' => 'advanced',
            'years_experience' => 5,
            'certifications' => 'Varsity Player 2022-2024',
            'last_used' => '2024-04-05',
        ]);

        StudentSkill::create([
            'student_id' => $student2->id,
            'name' => 'Time Management',
            'category' => 'soft',
            'proficiency' => 'beginner',
            'years_experience' => 1,
            'certifications' => null,
            'last_used' => '2024-04-01',
        ]);

        // Add Affiliations for Jose Reyes
        StudentAffiliation::create([
            'student_id' => $student2->id,
            'name' => 'CCS Basketball Team',
            'type' => 'student_organization',
            'role' => 'Player',
            'start_date' => '2022-06-01',
            'position' => 'Point Guard',
            'description' => 'Varsity basketball team member',
        ]);

        StudentAffiliation::create([
            'student_id' => $student2->id,
            'name' => 'Programming Club',
            'type' => 'student_organization',
            'role' => 'Member',
            'start_date' => '2023-09-01',
            'position' => null,
            'description' => 'Participating in coding competitions and workshops',
        ]);

        // Add Violations for Jose Reyes (At Risk Student)
        StudentViolation::create([
            'student_id' => $student2->id,
            'type' => 'attendance',
            'description' => 'Excessive absences in Calculus class',
            'date' => '2024-03-10',
            'severity' => 'moderate',
            'status' => 'pending',
            'consequence' => 'Warning letter sent to parents',
        ]);

        StudentViolation::create([
            'student_id' => $student2->id,
            'type' => 'academic',
            'description' => 'Late submission of major project',
            'date' => '2024-03-25',
            'severity' => 'minor',
            'status' => 'resolved',
            'consequence' => '10% grade deduction',
        ]);

        StudentViolation::create([
            'student_id' => $student2->id,
            'type' => 'disciplinary',
            'description' => 'Disruptive behavior during lecture',
            'date' => '2024-04-05',
            'severity' => 'moderate',
            'status' => 'pending',
            'consequence' => 'Counseling session required',
        ]);

        // Add minor violation for Maria (Good standing student)
        StudentViolation::create([
            'student_id' => $student1->id,
            'type' => 'attendance',
            'description' => 'Late arrival in one class',
            'date' => '2024-02-15',
            'severity' => 'minor',
            'status' => 'resolved',
            'consequence' => 'Verbal warning',
        ]);

        $this->command->info('Student users created:');
        $this->command->info('  - maria.student@ccs.edu / password (Good standing)');
        $this->command->info('  - jose.student@ccs.edu / password (At Risk Student)');
        $this->command->info('  - Added skills, affiliations, and violations for both students');
    }
}
