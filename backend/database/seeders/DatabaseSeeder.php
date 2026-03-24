<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Skill;
use App\Models\Talent;
use App\Models\Sport;
use App\Models\Certificate;
use App\Models\Violation;
use App\Models\Organization;
use App\Models\Announcement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Professor Users and Profiles
        $professorUser = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan.professor@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'professor',
        ]);

        $professor = Professor::create([
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

        // Create Student Users and Profiles
        $studentUser1 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria.student@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $student1 = Student::create([
            'student_unique_id' => 'STU-' . strtoupper(Str::random(8)),
            'user_id' => $studentUser1->id,
            'first_name' => 'Maria',
            'middle_name' => 'Reyes',
            'last_name' => 'Santos',
            'age' => 20,
            'blood_type' => 'A+',
            'disability_status' => false,
            'scholar' => true,
            'working_student' => false,
            'email' => 'maria.student@ccs.edu',
            'contact_number' => '+639987654321',
            'address' => '456 Avenue, City',
            'section' => 'BSIT-3A',
            'year_level' => '3rd',
            'college' => 'CCS',
            'program' => 'BSIT',
            'curriculum' => '2020-2024',
            'academic_status' => 'Regular',
        ]);

        $studentUser2 = User::create([
            'name' => 'Jose Reyes',
            'email' => 'jose.student@ccs.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $student2 = Student::create([
            'student_unique_id' => 'STU-' . strtoupper(Str::random(8)),
            'user_id' => $studentUser2->id,
            'first_name' => 'Jose',
            'middle_name' => 'Lopez',
            'last_name' => 'Reyes',
            'age' => 21,
            'blood_type' => 'B+',
            'disability_status' => false,
            'scholar' => false,
            'working_student' => true,
            'email' => 'jose.student@ccs.edu',
            'contact_number' => '+639123456788',
            'address' => '789 Boulevard, City',
            'section' => 'BSCS-2B',
            'year_level' => '2nd',
            'college' => 'CCS',
            'program' => 'BSCS',
            'curriculum' => '2020-2024',
            'academic_status' => 'Regular',
        ]);

        // Create Skills for Student 1
        Skill::create([
            'student_id' => $student1->id,
            'skill_name' => 'Web Development',
        ]);

        Skill::create([
            'student_id' => $student1->id,
            'skill_name' => 'Graphic Design',
        ]);

        // Create Skills for Student 2
        Skill::create([
            'student_id' => $student2->id,
            'skill_name' => 'Networking',
        ]);

        Skill::create([
            'student_id' => $student2->id,
            'skill_name' => 'Database Management',
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
        Violation::create([
            'student_id' => $student2->id,
            'violation_type' => 'Late Submission',
            'description' => 'Submitted project 3 days late',
            'sanction' => 'Warning Letter',
            'date_committed' => '2024-01-15',
            'status' => 'Pending',
        ]);

        Violation::create([
            'student_id' => $student2->id,
            'violation_type' => 'Attendance Issue',
            'description' => 'Absent for 5 consecutive classes',
            'sanction' => 'Parent Notification',
            'date_committed' => '2024-01-20',
            'status' => 'Pending',
        ]);

        Violation::create([
            'student_id' => $student2->id,
            'violation_type' => 'Code Violation',
            'description' => 'Plagiarism in assignment',
            'sanction' => 'Grade Penalty',
            'date_committed' => '2024-01-25',
            'status' => 'Pending',
        ]);

        Violation::create([
            'student_id' => $student1->id,
            'violation_type' => 'Minor Infraction',
            'description' => 'Late for class',
            'sanction' => 'Verbal Warning',
            'date_committed' => '2024-01-10',
            'status' => 'Resolved',
        ]);

        // Create Sample Announcements
        $announcement1 = Announcement::create([
            'title' => 'Welcome to CCS-ISPS System',
            'content' => 'We are excited to launch the new Integrated Student Profiling System! This platform will help us better track and support our students\' academic progress, talents, and overall development.',
            'image' => 'announcements/welcome.jpg',
            'user_id' => $admin->id,
            'status' => 'published',
            'target_type' => 'all',
            'target_users' => null,
        ]);

        $announcement2 = Announcement::create([
            'title' => 'Midterm Examination Schedule',
            'content' => 'Please be reminded that midterm examinations will start next week. Make sure to review your schedules and prepare accordingly. Good luck to all students!',
            'image' => 'announcements/exams.jpg',
            'user_id' => $professorUser->id,
            'status' => 'published',
            'target_type' => 'students',
            'target_users' => null,
        ]);

        $announcement3 = Announcement::create([
            'title' => 'Faculty Development Workshop',
            'content' => 'A professional development workshop on modern teaching methodologies will be held this Friday. All faculty members are required to attend.',
            'image' => 'announcements/workshop.jpg',
            'user_id' => $admin->id,
            'status' => 'published',
            'target_type' => 'professors',
            'target_users' => null,
        ]);

        // Mark some announcements as viewed by users
        $announcement1->markAsViewedBy($professorUser->id);
        $announcement1->markAsViewedBy($studentUser->id);
        $announcement2->markAsViewedBy($studentUser->id);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Login: admin@ccs.edu / password');
        $this->command->info('Professor Login: juan.professor@ccs.edu / password');
        $this->command->info('Student Login: maria.student@ccs.edu / password');
        $this->command->info('Student Login: jose.student@ccs.edu / password (At Risk Student)');
    }
}
