<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Professor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfessorSeeder extends Seeder
{
    public function run(): void
    {
        $professorUser = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan.professor@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'professor',
        ]);

        Professor::create([
            'professor_unique_id' => 'PROF-' . strtoupper(Str::random(8)),
            'user_id' => $professorUser->id,
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'age' => 35,
            'birthday' => '1989-05-15',
            'blood_type' => 'O+',
            'disability_status' => false,
            'gender' => 'Male',
            'email' => 'juan.professor@ccs.edu',
            'contact_number' => '+639123456789',
            'address' => '123 Street, City',
            'educational_attainment' => 'Master of Science in Computer Science',
            'experience' => '10 years in teaching and industry',
            'courses_handled' => 'Data Structures, Algorithm Analysis, Web Development',
            'role' => 'Associate Professor',
            'employment_type' => 'Full-time',
            'department' => 'CCS',
            'organization' => 'Computer Science Society',
            'application_date' => '2020-06-01',
        ]);

        $this->command->info('Professor user created: juan.professor@ccs.edu / password');
    }
}
