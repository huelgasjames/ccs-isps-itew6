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

        // Sample skills with categories
        $skills = [
            ['name' => 'JavaScript', 'category' => 'technical'],
            ['name' => 'Python', 'category' => 'technical'],
            ['name' => 'Java', 'category' => 'technical'],
            ['name' => 'C++', 'category' => 'technical'],
            ['name' => 'PHP', 'category' => 'technical'],
            ['name' => 'React', 'category' => 'technical'],
            ['name' => 'Vue.js', 'category' => 'technical'],
            ['name' => 'Node.js', 'category' => 'technical'],
            ['name' => 'HTML/CSS', 'category' => 'technical'],
            ['name' => 'SQL', 'category' => 'technical'],
            ['name' => 'MongoDB', 'category' => 'technical'],
            ['name' => 'Docker', 'category' => 'technical'],
            ['name' => 'Git', 'category' => 'technical'],
            ['name' => 'Machine Learning', 'category' => 'technical'],
            ['name' => 'Data Analysis', 'category' => 'technical'],
            ['name' => 'UI/UX Design', 'category' => 'creative'],
            ['name' => 'Laravel', 'category' => 'technical'],
            ['name' => 'Angular', 'category' => 'technical'],
            ['name' => 'TypeScript', 'category' => 'technical'],
            ['name' => 'Flutter', 'category' => 'technical'],
            ['name' => 'Swift', 'category' => 'technical'],
            ['name' => 'Kotlin', 'category' => 'technical'],
            ['name' => 'AWS', 'category' => 'technical'],
            ['name' => 'Azure', 'category' => 'technical'],
            ['name' => 'Google Cloud', 'category' => 'technical'],
            ['name' => 'TensorFlow', 'category' => 'technical'],
            ['name' => 'PyTorch', 'category' => 'technical'],
            ['name' => 'Leadership', 'category' => 'soft'],
            ['name' => 'Communication', 'category' => 'soft'],
            ['name' => 'Teamwork', 'category' => 'soft'],
        ];

        $proficiencyLevels = ['beginner', 'intermediate', 'advanced', 'expert'];

        foreach ($students as $student) {
            // Assign 3-5 random skills to each student
            $studentSkills = collect($skills)->random(rand(3, 5));
            
            foreach ($studentSkills as $skill) {
                StudentSkill::create([
                    'student_id' => $student->id,
                    'name' => $skill['name'],
                    'category' => $skill['category'],
                    'proficiency' => $proficiencyLevels[array_rand($proficiencyLevels)],
                    'years_experience' => rand(0, 5),
                    'certifications' => rand(0, 1) === 1 ? 'Certified' : null,
                    'last_used' => now()->subMonths(rand(0, 12)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
