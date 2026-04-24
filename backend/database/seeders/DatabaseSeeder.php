<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Seed core users (must be in order: admin -> professor -> students)
        $this->call([
            AdminSeeder::class,
            ProfessorSeeder::class,
            StudentSeeder::class,
        ]);

        // Seed student basic data (skills, talents, sports, certificates, organizations, violations)
        $this->call(StudentBasicDataSeeder::class);

        // Seed announcements
        $this->call(AnnouncementSeeder::class);

        // Seed additional student data
        $this->call([
            CourseSeeder::class,
            CourseEnrollmentSeeder::class,
            EventSeeder::class,
            StudentSkillSeeder::class,
            StudentActivitySeeder::class,
            StudentAcademicHistorySeeder::class,
            StudentAffiliationSeeder::class,
            StudentViolationSeeder::class,
        ]);

        // Seed dummy students (1000 records)
        $this->call(DummyStudentSeeder::class);

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Database seeded successfully!');
        $this->command->info('========================================');
        $this->command->info('Login Credentials:');
        $this->command->info('  Admin:     admin@ccs.edu / password');
        $this->command->info('  Professor: juan.professor@ccs.edu / password');
        $this->command->info('  Student 1: maria.student@ccs.edu / password (Good standing)');
        $this->command->info('  Student 2: jose.student@ccs.edu / password (At Risk - 3 violations)');
        $this->command->info('========================================');
    }
}
