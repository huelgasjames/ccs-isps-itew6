<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@ccs.edu')->first();
        
        if (!$admin) {
            $this->command->error('Admin user not found. Please run AdminSeeder first.');
            return;
        }

        $events = [
            [
                'title' => 'CCS Freshmen Orientation',
                'description' => 'Welcome and orientation program for first-year CCS students',
                'type' => 'academic',
                'start_datetime' => '2024-06-15 09:00:00',
                'end_datetime' => '2024-06-15 12:00:00',
                'venue' => 'CCS Auditorium',
                'max_participants' => 200,
                'status' => 'upcoming',
                'organizer' => 'CCS Department',
                'target_audience' => 'all_students',
            ],
            [
                'title' => 'Programming Competition 2024',
                'description' => 'Annual programming competition for CCS students',
                'type' => 'curricular',
                'start_datetime' => '2024-07-20 08:00:00',
                'end_datetime' => '2024-07-20 17:00:00',
                'venue' => 'Computer Laboratory',
                'max_participants' => 50,
                'status' => 'upcoming',
                'organizer' => 'Programming Club',
                'target_audience' => 'all_students',
            ],
            [
                'title' => 'Career Fair 2024',
                'description' => 'Job fair featuring IT companies and startups',
                'type' => 'extra_curricular',
                'start_datetime' => '2024-08-10 09:00:00',
                'end_datetime' => '2024-08-10 16:00:00',
                'venue' => 'University Gymnasium',
                'max_participants' => 500,
                'status' => 'upcoming',
                'organizer' => 'Student Affairs Office',
                'target_audience' => 'all',
            ],
            [
                'title' => 'Web Development Workshop',
                'description' => 'Hands-on workshop on modern web development technologies',
                'type' => 'workshop',
                'start_datetime' => '2024-06-25 13:00:00',
                'end_datetime' => '2024-06-25 17:00:00',
                'venue' => 'Computer Laboratory 2',
                'max_participants' => 30,
                'status' => 'upcoming',
                'organizer' => 'Web Development Guild',
                'target_audience' => 'all_students',
            ],
            [
                'title' => 'Alumni Homecoming',
                'description' => 'Annual gathering of CCS alumni',
                'type' => 'cultural',
                'start_datetime' => '2024-12-15 18:00:00',
                'end_datetime' => '2024-12-15 22:00:00',
                'venue' => 'University Ballroom',
                'max_participants' => 150,
                'status' => 'upcoming',
                'organizer' => 'CCS Alumni Association',
                'target_audience' => 'all',
            ],
            [
                'title' => 'Research Symposium',
                'description' => 'Presentation of student and faculty research projects',
                'type' => 'academic',
                'start_datetime' => '2024-09-05 09:00:00',
                'end_datetime' => '2024-09-05 17:00:00',
                'venue' => 'Conference Hall',
                'max_participants' => 100,
                'status' => 'upcoming',
                'organizer' => 'Research Office',
                'target_audience' => 'all',
            ],
            [
                'title' => 'Hackathon 2024',
                'description' => '24-hour coding competition and innovation challenge',
                'type' => 'curricular',
                'start_datetime' => '2024-10-18 09:00:00',
                'end_datetime' => '2024-10-19 09:00:00',
                'venue' => 'Innovation Lab',
                'max_participants' => 40,
                'status' => 'upcoming',
                'organizer' => 'Computer Science Society',
                'target_audience' => 'all_students',
            ],
            [
                'title' => 'Faculty Development Seminar',
                'description' => 'Professional development for CCS faculty members',
                'type' => 'seminar',
                'start_datetime' => '2024-07-05 09:00:00',
                'end_datetime' => '2024-07-05 16:00:00',
                'venue' => 'Faculty Room',
                'max_participants' => 25,
                'status' => 'upcoming',
                'organizer' => 'CCS Department',
                'target_audience' => 'faculty',
            ],
        ];

        foreach ($events as $event) {
            Event::create([
                'title' => $event['title'],
                'description' => $event['description'],
                'type' => $event['type'],
                'start_datetime' => $event['start_datetime'],
                'end_datetime' => $event['end_datetime'],
                'venue' => $event['venue'],
                'max_participants' => $event['max_participants'],
                'current_participants' => 0,
                'status' => $event['status'],
                'organizer' => $event['organizer'],
                'target_audience' => $event['target_audience'],
                'registration_fee' => 0,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
