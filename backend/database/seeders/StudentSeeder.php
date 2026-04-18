<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Create Student 1 - Maria Santos (Good standing)
        $studentUser1 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria.student@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        Student::create([
            'student_id' => '2024-001',
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

        // Create Student 2 - Jose Reyes (At risk)
        $studentUser2 = User::create([
            'name' => 'Jose Reyes',
            'email' => 'jose.student@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        Student::create([
            'student_id' => '2024-002',
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

        $this->command->info('Student users created:');
        $this->command->info('  - maria.student@ccs.edu / password (Good standing)');
        $this->command->info('  - jose.student@ccs.edu / password (At Risk Student)');
    }
}
