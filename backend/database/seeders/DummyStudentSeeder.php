<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\StudentActivity;
use App\Models\StudentAcademicHistory;
use App\Models\StudentAffiliation;
use App\Models\StudentViolation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyStudentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating 1000 dummy students...');
        
        // Get the last student ID to continue numbering
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $startingId = $lastStudent ? $lastStudent->id + 1 : 1;
        
        // Sample data arrays
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Carlos', 'Sofia', 'Miguel', 'Isabella', 'Diego', 'Valentina', 'Andres', 'Camila', 'Luis', 'Lucia', 'Roberto', 'Gabriela', 'Fernando', 'Martina', 'Ricardo', 'Paula'];
        $lastNames = ['Garcia', 'Rodriguez', 'Lopez', 'Martinez', 'Gonzalez', 'Perez', 'Sanchez', 'Ramirez', 'Cruz', 'Flores', 'Torres', 'Rivera', 'Morales', 'Reyes', 'Jimenez', 'Castillo', 'Vargas', 'Mendoza', 'Silva', 'Alvarez'];
        $departments = ['CCS', 'Engineering', 'Business', 'Arts', 'Science', 'Medicine', 'Law', 'Education'];
        $skills = [
            ['Programming', 'technical', 'intermediate', 'JavaScript, Python'],
            ['Web Design', 'technical', 'beginner', 'HTML, CSS'],
            ['Data Analysis', 'technical', 'advanced', 'Excel, R, Python'],
            ['Communication', 'soft', 'advanced', 'Public speaking, writing'],
            ['Leadership', 'soft', 'intermediate', 'Team management'],
            ['Database Management', 'technical', 'intermediate', 'MySQL, MongoDB'],
            ['Machine Learning', 'technical', 'beginner', 'Python, TensorFlow'],
            ['Graphic Design', 'technical', 'advanced', 'Photoshop, Illustrator'],
            ['Project Management', 'soft', 'advanced', 'Agile, Scrum'],
            ['Research', 'soft', 'intermediate', 'Academic writing']
        ];
        $activities = [
            ['Basketball Club', 'sports', 'member', '2023-09-01', '2024-05-31', 'Regular member', 'Team player', 'local'],
            ['Debate Team', 'organization', 'leader', '2023-08-15', '2024-06-30', 'Team captain', 'Won 3 competitions', 'regional'],
            ['Science Fair', 'research', 'participant', '2024-01-20', '2024-01-25', 'Project presentation', 'Honorable mention', 'local'],
            ['Volunteer Work', 'volunteer', 'volunteer', '2023-10-01', null, 'Community service', '50+ hours', 'local'],
            ['Programming Contest', 'research', 'winner', '2024-03-15', '2024-03-15', 'First place', 'Algorithm challenge', 'national'],
            ['Music Club', 'organization', 'member', '2023-09-01', '2024-06-30', 'Piano player', 'Performed in recital', 'local'],
            ['Student Government', 'organization', 'treasurer', '2023-09-01', '2024-06-30', 'Financial management', 'Managed club budget', 'local'],
            ['Research Assistant', 'research', 'assistant', '2024-02-01', '2024-05-31', 'Data collection', 'Published paper', 'international']
        ];
        
        $batchSize = 100;
        $totalStudents = 1000;
        
        for ($batch = 0; $batch < $totalStudents / $batchSize; $batch++) {
            $this->command->info("Creating batch " . ($batch + 1) . " of " . ceil($totalStudents / $batchSize));
            
            for ($i = 0; $i < $batchSize; $i++) {
                $studentNumber = $startingId + ($batch * $batchSize) + $i;
                
                // Create user
                $user = User::create([
                    'name' => $this->generateRandomName($firstNames, $lastNames),
                    'email' => $this->generateUniqueEmail($studentNumber),
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                ]);
                
                // Create student
                $student = Student::create([
                    'user_id' => $user->id,
                    'student_id' => 'STU-' . str_pad($studentNumber, 6, '0', STR_PAD_LEFT),
                    'first_name' => $firstNames[array_rand($firstNames)],
                    'middle_name' => rand(0, 1) ? $firstNames[array_rand($firstNames)] : null,
                    'last_name' => $lastNames[array_rand($lastNames)],
                    'email' => $user->email,
                    'phone' => '+639' . rand(10000000, 99999999),
                    'date_of_birth' => $this->generateRandomDate(1998, 2005),
                    'age' => rand(17, 25),
                    'gender' => ['male', 'female', 'other'][array_rand(['male', 'female', 'other'])],
                    'address' => 'Street ' . rand(1, 999) . ', ' . $this->generateRandomCity(),
                    'city' => $this->generateRandomCity(),
                    'province' => $this->generateRandomProvince(),
                    'postal_code' => rand(1000, 9999),
                    'emergency_contact_name' => $this->generateRandomName($firstNames, $lastNames),
                    'emergency_contact_relationship' => ['Parent', 'Sibling', 'Guardian', 'Relative'][array_rand(['Parent', 'Sibling', 'Guardian', 'Relative'])],
                    'emergency_contact_phone' => '+639' . rand(10000000, 99999999),
                    'current_year' => rand(1, 4),
                    'current_semester' => rand(1, 2),
                    'current_gpa' => round(rand(70, 95) / 10, 2),
                    'total_units' => rand(120, 180),
                    'standing' => ['excellent', 'good', 'average', 'probation'][array_rand(['excellent', 'good', 'average', 'probation'])],
                    'advisor' => 'Dr. ' . $lastNames[array_rand($lastNames)],
                    'is_active' => true,
                ]);
                
                // Add random skills (2-4 per student)
                $skillCount = rand(2, 4);
                $selectedSkills = $skillCount === 1 ? [array_rand($skills)] : array_rand($skills, $skillCount);
                foreach ($selectedSkills as $skillIndex) {
                    $skill = $skills[$skillIndex];
                    StudentSkill::create([
                        'student_id' => $student->id,
                        'name' => $skill[0],
                        'category' => $skill[1],
                        'proficiency' => $skill[2],
                        'certifications' => $skill[3],
                        'last_used' => $this->generateRandomDate(2023, 2024),
                    ]);
                }
                
                // Add random activities (1-3 per student)
                $activityCount = rand(1, 3);
                $selectedActivities = $activityCount === 1 ? [array_rand($activities)] : array_rand($activities, $activityCount);
                foreach ($selectedActivities as $activityIndex) {
                    $activity = $activities[$activityIndex];
                    StudentActivity::create([
                        'student_id' => $student->id,
                        'name' => $activity[0],
                        'type' => $activity[1],
                        'role' => $activity[2],
                        'start_date' => $activity[3],
                        'end_date' => $activity[4],
                        'description' => $activity[5],
                        'achievements' => $activity[6],
                        'level' => $activity[7] ?? 'local',
                    ]);
                }
                
                // Add academic history (1-2 entries per student)
                $historyCount = rand(1, 2);
                for ($h = 0; $h < $historyCount; $h++) {
                    $startYear = 2020 + $h;
                    $endYear = $startYear + 1;
                    
                    StudentAcademicHistory::create([
                        'student_id' => $student->id,
                        'school_name' => $this->generateRandomSchool(),
                        'degree' => ['Bachelor of Science', 'Bachelor of Arts', 'Bachelor of Engineering'][array_rand(['Bachelor of Science', 'Bachelor of Arts', 'Bachelor of Engineering'])],
                        'major' => ['Computer Science', 'Information Technology', 'Business Administration', 'Engineering', 'Psychology'][array_rand(['Computer Science', 'Information Technology', 'Business Administration', 'Engineering', 'Psychology'])],
                        'start_date' => $startYear . '-06-01',
                        'end_date' => $endYear . '-05-31',
                        'gpa' => round(rand(75, 95) / 10, 2),
                        'status' => 'completed',
                        'honors' => rand(0, 1) ? ['Dean\'s List', 'Cum Laude', 'Magna Cum Laude'][array_rand(['Dean\'s List', 'Cum Laude', 'Magna Cum Laude'])] : null,
                    ]);
                }
                
                // Add affiliations (0-2 per student)
                if (rand(0, 1)) {
                    $affiliationCount = rand(1, 2);
                    for ($a = 0; $a < $affiliationCount; $a++) {
                        StudentAffiliation::create([
                            'student_id' => $student->id,
                            'name' => $this->generateRandomOrganization(),
                            'type' => ['student_organization', 'department', 'professional', 'community'][array_rand(['student_organization', 'department', 'professional', 'community'])],
                            'role' => ['member', 'officer', 'president', 'treasurer'][array_rand(['member', 'officer', 'president', 'treasurer'])],
                            'position' => rand(0, 1) ? ['President', 'Vice President', 'Secretary', 'Member'][array_rand(['President', 'Vice President', 'Secretary', 'Member'])] : null,
                            'start_date' => $this->generateRandomDate(2023, 2024),
                            'description' => 'Active member of the organization',
                        ]);
                    }
                }
                
                // Add violations (0-2 per student, mostly none)
                if (rand(0, 10) < 3) { // 30% chance of having violations
                    $violationCount = rand(1, 2);
                    for ($v = 0; $v < $violationCount; $v++) {
                        StudentViolation::create([
                            'student_id' => $student->id,
                            'type' => ['academic', 'disciplinary', 'attendance', 'conduct'][array_rand(['academic', 'disciplinary', 'attendance', 'conduct'])],
                            'description' => $this->generateRandomViolation(),
                            'date' => $this->generateRandomDate(2023, 2024),
                            'severity' => ['minor', 'moderate', 'major', 'severe'][array_rand(['minor', 'moderate', 'major', 'severe'])],
                            'status' => ['pending', 'resolved', 'appealed'][array_rand(['pending', 'resolved', 'appealed'])],
                            'consequence' => rand(0, 1) ? ['Warning', 'Probation', 'Community Service'][array_rand(['Warning', 'Probation', 'Community Service'])] : null,
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('Successfully created ' . $totalStudents . ' dummy students with related data!');
    }
    
    private function generateRandomName($firstNames, $lastNames): string
    {
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }
    
    private function generateUniqueEmail($studentNumber): string
    {
        $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
        return 'student' . $studentNumber . '_' . strtolower(Str::random(5)) . '@' . $domains[array_rand($domains)];
    }
    
    private function generateRandomDate($startYear, $endYear): string
    {
        $year = rand($startYear, $endYear);
        $month = rand(1, 12);
        $day = rand(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
    
    private function generateRandomCity(): string
    {
        $cities = ['Manila', 'Quezon City', 'Cebu City', 'Davao City', 'Caloocan', 'Makati', 'Pasig', 'Taguig'];
        return $cities[array_rand($cities)];
    }
    
    private function generateRandomProvince(): string
    {
        $provinces = ['Metro Manila', 'Cebu', 'Davao del Sur', 'Laguna', 'Rizal', 'Bulacan', 'Cavite'];
        return $provinces[array_rand($provinces)];
    }
    
    private function generateRandomSchool(): string
    {
        $schools = [
            'University of the Philippines',
            'Ateneo de Manila University',
            'De La Salle University',
            'University of Santo Tomas',
            'Polytechnic University of the Philippines',
            'University of the East',
            'Far Eastern University',
            'Adamson University'
        ];
        return $schools[array_rand($schools)];
    }
    
    private function generateRandomOrganization(): string
    {
        $organizations = [
            'Student Council',
            'Computer Science Society', 
            'Engineering Club',
            'Business Administration Association',
            'Psychology Society',
            'Arts and Culture Club',
            'Sports Association',
            'Environmental Club'
        ];
        return $organizations[array_rand($organizations)];
    }
    
    private function generateRandomViolation(): string
    {
        $violations = [
            'Late submission of assignment',
            'Unauthorized absence from class',
            'Library book overdue',
            'Violation of dress code',
            'Use of mobile phone during examination',
            'Plagiarism in assignment',
            'Disruptive behavior in class',
            'Failure to attend required event'
        ];
        return $violations[array_rand($violations)];
    }
}
