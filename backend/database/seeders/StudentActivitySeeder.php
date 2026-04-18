<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentActivity;
use Illuminate\Database\Seeder;

class StudentActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Get existing students
        $students = Student::all();

        // Sample activities
        $activities = [
            ['name' => 'Basketball Tournament', 'type' => 'sports', 'role' => 'Player', 'level' => 'regional'],
            ['name' => 'Programming Contest', 'type' => 'research', 'role' => 'Participant', 'level' => 'national'],
            ['name' => 'Student Council', 'type' => 'organization', 'role' => 'Member', 'level' => 'local'],
            ['name' => 'Web Development Workshop', 'type' => 'research', 'role' => 'Attendee', 'level' => 'local'],
            ['name' => 'Community Outreach', 'type' => 'volunteer', 'role' => 'Volunteer', 'level' => 'local'],
            ['name' => 'Hackathon 2024', 'type' => 'research', 'role' => 'Team Lead', 'level' => 'national'],
            ['name' => 'Debate Competition', 'type' => 'research', 'role' => 'Participant', 'level' => 'regional'],
            ['name' => 'Music Festival', 'type' => 'club', 'role' => 'Performer', 'level' => 'local'],
            ['name' => 'Research Symposium', 'type' => 'research', 'role' => 'Presenter', 'level' => 'international'],
            ['name' => 'Environmental Campaign', 'type' => 'volunteer', 'role' => 'Organizer', 'level' => 'local'],
        ];

        foreach ($students as $student) {
            // Assign 2-4 random activities to each student
            $studentActivities = collect($activities)->random(rand(2, 4));
            
            foreach ($studentActivities as $activity) {
                StudentActivity::create([
                    'student_id' => $student->id,
                    'name' => $activity['name'],
                    'type' => $activity['type'],
                    'role' => $activity['role'],
                    'level' => $activity['level'],
                    'start_date' => now()->subDays(rand(30, 365)),
                    'end_date' => rand(0, 1) ? now()->subDays(rand(1, 29)) : null,
                    'description' => 'Participated in ' . $activity['name'] . ' as ' . $activity['role'],
                    'achievements' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
