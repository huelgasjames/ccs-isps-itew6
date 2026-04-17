<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentSkill;
use Illuminate\Database\Seeder;

class StudentSkillSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing students
        $students = Student::all();

        // Sample skills for students
        $skills = [
            'JavaScript', 'Python', 'Java', 'C++', 'PHP', 'React', 'Vue.js', 
            'Node.js', 'HTML/CSS', 'SQL', 'MongoDB', 'Docker', 'Git', 
            'Machine Learning', 'Data Analysis', 'UI/UX Design', 'Laravel',
            'Angular', 'TypeScript', 'Flutter', 'Swift', 'Kotlin', 'AWS',
            'Azure', 'Google Cloud', 'TensorFlow', 'PyTorch'
        ];

        foreach ($students as $student) {
            // Assign 3-5 random skills to each student
            $studentSkills = collect($skills)->random(rand(3, 5));
            
            foreach ($studentSkills as $skillName) {
                StudentSkill::create([
                    'student_id' => $student->id,
                    'skill_name' => $skillName,
                    'proficiency_level' => rand(1, 5), // 1-5 scale
                    'years_experience' => rand(0, 4),
                    'certified' => rand(0, 1) === 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
