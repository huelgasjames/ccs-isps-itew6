<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'CCS Freshmen Orientation',
                'description' => 'Welcome and orientation program for first-year CCS students',
                'event_type' => 'Academic',
                'start_date' => '2024-06-15 09:00:00',
                'end_date' => '2024-06-15 12:00:00',
                'location' => 'CCS Auditorium',
                'max_participants' => 200,
                'status' => 'upcoming',
                'organizer' => 'CCS Department',
            ],
            [
                'title' => 'Programming Competition 2024',
                'description' => 'Annual programming competition for CCS students',
                'event_type' => 'Competition',
                'start_date' => '2024-07-20 08:00:00',
                'end_date' => '2024-07-20 17:00:00',
                'location' => 'Computer Laboratory',
                'max_participants' => 50,
                'status' => 'upcoming',
                'organizer' => 'Programming Club',
            ],
            [
                'title' => 'Career Fair 2024',
                'description' => 'Job fair featuring IT companies and startups',
                'event_type' => 'Career',
                'start_date' => '2024-08-10 09:00:00',
                'end_date' => '2024-08-10 16:00:00',
                'location' => 'University Gymnasium',
                'max_participants' => 500,
                'status' => 'upcoming',
                'organizer' => 'Student Affairs Office',
            ],
            [
                'title' => 'Web Development Workshop',
                'description' => 'Hands-on workshop on modern web development technologies',
                'event_type' => 'Workshop',
                'start_date' => '2024-06-25 13:00:00',
                'end_date' => '2024-06-25 17:00:00',
                'location' => 'Computer Laboratory 2',
                'max_participants' => 30,
                'status' => 'upcoming',
                'organizer' => 'Web Development Guild',
            ],
            [
                'title' => 'Alumni Homecoming',
                'description' => 'Annual gathering of CCS alumni',
                'event_type' => 'Social',
                'start_date' => '2024-12-15 18:00:00',
                'end_date' => '2024-12-15 22:00:00',
                'location' => 'University Ballroom',
                'max_participants' => 150,
                'status' => 'upcoming',
                'organizer' => 'CCS Alumni Association',
            ],
            [
                'title' => 'Research Symposium',
                'description' => 'Presentation of student and faculty research projects',
                'event_type' => 'Academic',
                'start_date' => '2024-09-05 09:00:00',
                'end_date' => '2024-09-05 17:00:00',
                'location' => 'Conference Hall',
                'max_participants' => 100,
                'status' => 'upcoming',
                'organizer' => 'Research Office',
            ],
            [
                'title' => 'Hackathon 2024',
                'description' => '24-hour coding competition and innovation challenge',
                'event_type' => 'Competition',
                'start_date' => '2024-10-18 09:00:00',
                'end_date' => '2024-10-19 09:00:00',
                'location' => 'Innovation Lab',
                'max_participants' => 40,
                'status' => 'upcoming',
                'organizer' => 'Computer Science Society',
            ],
            [
                'title' => 'Faculty Development Seminar',
                'description' => 'Professional development for CCS faculty members',
                'event_type' => 'Professional',
                'start_date' => '2024-07-05 09:00:00',
                'end_date' => '2024-07-05 16:00:00',
                'location' => 'Faculty Room',
                'max_participants' => 25,
                'status' => 'upcoming',
                'organizer' => 'CCS Department',
            ],
        ];

        foreach ($events as $event) {
            Event::create([
                'title' => $event['title'],
                'description' => $event['description'],
                'event_type' => $event['event_type'],
                'start_date' => $event['start_date'],
                'end_date' => $event['end_date'],
                'location' => $event['location'],
                'max_participants' => $event['max_participants'],
                'status' => $event['status'],
                'organizer' => $event['organizer'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
