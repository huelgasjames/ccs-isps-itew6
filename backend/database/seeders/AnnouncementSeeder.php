<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users
        $admin = User::where('email', 'admin@ccs.edu')->first();
        $professorUser = User::where('email', 'juan.professor@ccs.edu')->first();
        $studentUser = User::where('email', 'maria.student@ccs.edu')->first();

        if (!$admin || !$professorUser || !$studentUser) {
            $this->command->error('Required users not found. Please run the main seeder first.');
            return;
        }

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

        $this->command->info('Announcements seeded successfully!');
        $this->command->info('Created 3 sample announcements with view tracking.');
    }
}
