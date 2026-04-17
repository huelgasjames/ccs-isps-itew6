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
            ['name' => 'Basketball Tournament', 'type' => 'Sports', 'role' => 'Player', 'status' => 'Completed'],
            ['name' => 'Programming Contest', 'type' => 'Academic', 'role' => 'Participant', 'status' => 'Completed'],
            ['name' => 'Student Council', 'type' => 'Leadership', 'role' => 'Member', 'status' => 'Ongoing'],
            ['name' => 'Web Development Workshop', 'type' => 'Workshop', 'role' => 'Attendee', 'status' => 'Completed'],
            ['name' => 'Community Outreach', 'type' => 'Volunteer', 'role' => 'Volunteer', 'status' => 'Ongoing'],
            ['name' => 'Hackathon 2024', 'type' => 'Competition', 'role' => 'Team Lead', 'status' => 'Completed'],
            ['name' => 'Debate Competition', 'type' => 'Academic', 'role' => 'Participant', 'status' => 'Completed'],
            ['name' => 'Music Festival', 'type' => 'Cultural', 'role' => 'Performer', 'status' => 'Completed'],
            ['name' => 'Research Symposium', 'type' => 'Academic', 'role' => 'Presenter', 'status' => 'Upcoming'],
            ['name' => 'Environmental Campaign', 'type' => 'Advocacy', 'role' => 'Organizer', 'status' => 'Ongoing'],
        ];

        foreach ($students as $student) {
            // Assign 2-4 random activities to each student
            $studentActivities = collect($activities)->random(rand(2, 4));
            
            foreach ($studentActivities as $activity) {
                StudentActivity::create([
                    'student_id' => $student->id,
                    'activity_name' => $activity['name'],
                    'activity_type' => $activity['type'],
                    'role' => $activity['role'],
                    'status' => $activity['status'],
                    'start_date' => now()->subDays(rand(30, 365)),
                    'end_date' => $activity['status'] === 'Completed' ? now()->subDays(rand(1, 29)) : null,
                    'description' => 'Participated in ' . $activity['name'] . ' as ' . $activity['role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
