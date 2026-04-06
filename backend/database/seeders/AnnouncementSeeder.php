<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\User;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for author
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->error('No admin user found. Please run the main seeder first.');
            return;
        }

        // Create sample announcements
        $announcements = [
            [
                'title' => 'System Maintenance Scheduled',
                'excerpt' => 'The system will undergo scheduled maintenance this weekend. Please save your work and log out before the maintenance period.',
                'content' => '# System Maintenance Scheduled

Dear Users,

We will be performing **scheduled system maintenance** this weekend to improve performance and security.

## Maintenance Schedule
- **Start**: Saturday, March 20, 2024 at 11:00 PM
- **End**: Sunday, March 21, 2024 at 3:00 AM
- **Duration**: 4 hours

## What to Expect
During the maintenance period:
- All services will be temporarily unavailable
- You will not be able to access the system
- Data will be safely backed up before maintenance begins

## Action Required
1. **Save all work** before Saturday evening
2. **Log out** of the system before 11:00 PM
3. **Plan accordingly** for the downtime

## Support
If you have any questions or concerns, please contact the IT support team.

Thank you for your cooperation!',
                'type' => 'System',
                'priority' => 'high',
                'status' => 'published',
                'author' => $admin->name,
                'publish_date' => now()->subDays(2),
                'expires_at' => now()->addDays(5),
                'target_all' => true,
                'target_students' => true,
                'target_professors' => true,
                'target_admins' => true,
                'user_id' => $admin->id,
                'views' => 156,
                'likes' => 23,
                'comments' => 12,
            ],
            [
                'title' => 'New Academic Calendar Released',
                'excerpt' => 'The academic calendar for the upcoming semester has been released. Please check the important dates and deadlines.',
                'content' => '# Academic Calendar Released

Dear Students and Faculty,

The academic calendar for the upcoming semester has been officially released. Please review the important dates and plan accordingly.

## Important Dates
- **Registration Period**: March 25 - April 5, 2024
- **First Day of Classes**: April 8, 2024
- **Midterm Exams**: May 20-24, 2024
- **Final Exams**: June 10-14, 2024
- **Last Day of Classes**: June 7, 2024

## Deadlines
- **Add/Drop Period**: April 15, 2024
- **Withdrawal Deadline**: May 15, 2024
- **Grade Appeals**: June 21, 2024

## Holiday Schedule
- **Labor Day**: May 1, 2024 (No Classes)
- **Easter Break**: April 29-30, 2024

Please mark these dates in your calendar and plan your academic activities accordingly.',
                'type' => 'Academic',
                'priority' => 'medium',
                'status' => 'published',
                'author' => $admin->name,
                'publish_date' => now()->subDays(3),
                'expires_at' => now()->addDays(30),
                'target_all' => true,
                'target_students' => true,
                'target_professors' => true,
                'target_admins' => true,
                'user_id' => $admin->id,
                'views' => 89,
                'likes' => 15,
                'comments' => 5,
            ],
            [
                'title' => 'Student Portal Update',
                'excerpt' => 'New features have been added to the student portal including improved navigation and faster loading times.',
                'content' => '# Student Portal Update

Dear Students,

We are excited to announce significant improvements to the student portal based on your feedback!

## New Features
- **Improved Navigation**: Easier access to all your academic information
- **Faster Loading**: 50% improvement in page load times
- **Mobile Responsive**: Better experience on mobile devices
- **Real-time Updates**: Instant notifications for grades and announcements
- **Enhanced Search**: Find your courses and resources quickly

## What\'s Changed
- Redesigned dashboard with quick access to important information
- New course overview page with all materials in one place
- Improved grade visualization and progress tracking
- Better calendar integration with your personal schedule

## How to Access
Simply log in to the student portal using your existing credentials. All your data has been preserved and migrated to the new system.

## Support
If you encounter any issues or have questions about the new features, please contact our IT support team.

We hope you enjoy these improvements!',
                'type' => 'Feature',
                'priority' => 'low',
                'status' => 'published',
                'author' => $admin->name,
                'publish_date' => now()->subDays(4),
                'expires_at' => null,
                'target_all' => true,
                'target_students' => true,
                'target_professors' => false,
                'target_admins' => false,
                'user_id' => $admin->id,
                'views' => 234,
                'likes' => 45,
                'comments' => 8,
            ],
            [
                'title' => 'Upcoming Career Fair',
                'excerpt' => 'Join us for the annual CCS Career Fair on April 15, 2024. Meet with top companies and explore job opportunities.',
                'content' => '# CCS Career Fair 2024

Dear Students,

Mark your calendars for the biggest career event of the year!

## Event Details
- **Date**: April 15, 2024
- **Time**: 9:00 AM - 4:00 PM
- **Location**: CCS Building, Ground Floor
- **Dress Code**: Business Casual

## Participating Companies
- Tech Giants Inc.
- Software Solutions Ltd.
- Digital Innovations Corp.
- Web Development Studios
- And many more!

## What to Bring
- Updated resume (multiple copies)
- Professional portfolio (if applicable)
- Business cards
- Questions for recruiters

## Workshop Schedule
- **9:00 AM**: Registration & Coffee
- **10:00 AM**: Resume Writing Workshop
- **11:00 AM**: Company Presentations
- **1:00 PM**: Networking Lunch
- **2:00 PM**: One-on-One Interviews
- **3:00 PM**: Career Q&A Panel

## Registration
Register online through the student portal by April 10, 2024. Limited slots available!

Don\'t miss this opportunity to jumpstart your career!',
                'type' => 'Event',
                'priority' => 'medium',
                'status' => 'published',
                'author' => $admin->name,
                'publish_date' => now()->subDays(1),
                'expires_at' => now()->addDays(14),
                'target_all' => true,
                'target_students' => true,
                'target_professors' => false,
                'target_admins' => false,
                'user_id' => $admin->id,
                'views' => 167,
                'likes' => 78,
                'comments' => 23,
            ],
            [
                'title' => 'New Computer Lab Policy',
                'excerpt' => 'Updated policies for computer lab usage including new booking system and extended hours.',
                'content' => '# Computer Lab Policy Update

Dear Students,

We are implementing new policies to improve computer lab accessibility and management.

## New Booking System
- **Online Booking**: Reserve lab time through the student portal
- **Maximum Duration**: 2 hours per session
- **Advance Booking**: Up to 3 days in advance
- **Cancellation**: 2 hours notice required

## Lab Hours
- **Monday-Friday**: 8:00 AM - 9:00 PM
- **Saturday**: 9:00 AM - 6:00 PM
- **Sunday**: 10:00 AM - 4:00 PM

## Usage Guidelines
- **Priority**: Course-related work gets priority
- **No Food or Drinks**: Water bottles allowed
- **Clean Workspace**: Leave your area clean
- **Save Work**: Use cloud storage or USB drives
- **Report Issues**: Notify lab staff immediately

## Software Available
- All required course software
- Microsoft Office Suite
- Adobe Creative Cloud
- Programming IDEs
- Statistical analysis tools

## Consequences
- **First Violation**: Warning
- **Second Violation**: 1-week suspension
- **Third Violation**: 1-month suspension

These policies are effective immediately. Thank you for your cooperation!',
                'type' => 'Policy',
                'priority' => 'medium',
                'status' => 'published',
                'author' => $admin->name,
                'publish_date' => now()->subHours(6),
                'expires_at' => null,
                'target_all' => true,
                'target_students' => true,
                'target_professors' => true,
                'target_admins' => true,
                'user_id' => $admin->id,
                'views' => 45,
                'likes' => 12,
                'comments' => 7,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        $this->command->info('Announcements seeded successfully!');
    }
}
