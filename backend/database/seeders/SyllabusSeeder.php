<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Professor;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Database\Seeder;

class SyllabusSeeder extends Seeder
{
    public function run(): void
    {
        // Get courses and professors
        $courses = Course::all();
        $professors = Professor::all();
        $admin = User::where('email', 'admin@ccs.edu')->first();

        if ($courses->isEmpty() || $professors->isEmpty()) {
            $this->command->error('Courses or Professors not found. Please run CourseSeeder and ProfessorSeeder first.');
            return;
        }

        // Create syllabi for each course
        foreach ($courses as $index => $course) {
            $professor = $professors[$index % $professors->count()];
            
            Syllabus::create([
                'course_id' => $course->id,
                'professor_id' => $professor->id,
                'academic_year' => '2024-2025',
                'semester' => $course->semester,
                'course_description' => $course->description . ' This course provides comprehensive coverage of fundamental concepts and practical applications.',
                'learning_objectives' => json_encode([
                    'Understand core concepts and principles',
                    'Develop practical skills and techniques',
                    'Apply knowledge to real-world problems',
                    'Enhance critical thinking and problem-solving abilities'
                ]),
                'topics_outline' => json_encode([
                    'Week 1-2: Introduction and Fundamentals',
                    'Week 3-4: Core Concepts and Theory',
                    'Week 5-6: Practical Applications',
                    'Week 7-8: Advanced Topics',
                    'Week 9-10: Project Development',
                    'Week 11-12: Review and Assessment',
                    'Week 13-14: Final Presentations',
                    'Week 15-16: Comprehensive Exam'
                ]),
                'assessment_methods' => json_encode([
                    'Quizzes' => '20%',
                    'Assignments' => '25%',
                    'Midterm Exam' => '25%',
                    'Final Project' => '20%',
                    'Class Participation' => '10%'
                ]),
                'grading_policy' => 'A: 90-100%, B: 85-89%, C: 80-84%, D: 75-79%, F: Below 75%',
                'required_materials' => 'Textbook, laptop, internet access, specific software tools',
                'class_policies' => 'Attendance is mandatory. Late submissions incur 10% penalty per day. Academic integrity is strictly enforced.',
                'file_path' => 'syllabi/' . $course->course_code . '_syllabus.pdf',
                'approved' => true,
                'approved_at' => now(),
                'approved_by' => $admin->id,
            ]);
        }

        $this->command->info('Syllabi seeded successfully!');
    }
}
