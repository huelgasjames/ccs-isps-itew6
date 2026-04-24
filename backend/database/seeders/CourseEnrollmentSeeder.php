<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use App\Models\CourseEnrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Enrolling students in courses...');
        
        // Get all courses and students
        $courses = Course::all();
        $students = Student::all();
        
        if ($courses->isEmpty() || $students->isEmpty()) {
            $this->command->error('No courses or students found. Please run CourseSeeder and StudentSeeder first.');
            return;
        }
        
        $this->command->info("Found {$courses->count()} courses and {$students->count()} students");
        
        // Clear existing enrollments
        CourseEnrollment::truncate();
        
        $enrollments = [];
        $currentAcademicYear = '2024-2025';
        $semesters = ['First Semester', 'Second Semester'];
        
        foreach ($students as $student) {
            // Determine student's year level
            $studentYearLevel = $this->getStudentYearLevel($student);
            
            // Get appropriate courses for this student's year level
            $availableCourses = $courses->filter(function ($course) use ($studentYearLevel) {
                return $course->year_level === $studentYearLevel || 
                       in_array($course->year_level, ['All', $studentYearLevel]);
            });
            
            // Enroll student in 4-6 courses per semester
            foreach ($semesters as $semester) {
                $semesterCourses = $availableCourses->filter(function ($course) use ($semester) {
                    return $course->semester === $semester && $course->status === 'active';
                });
                
                // Take 4-6 random courses for this semester
                $coursesToEnroll = $semesterCourses->random(min(5, $semesterCourses->count()));
                
                foreach ($coursesToEnroll as $course) {
                    // Check if course has capacity
                    $currentEnrollments = CourseEnrollment::where('course_id', $course->id)
                                                        ->where('semester', $semester)
                                                        ->where('academic_year', $currentAcademicYear)
                                                        ->count();
                    
                    if ($currentEnrollments < $course->max_students) {
                        $enrollments[] = [
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                            'semester' => $semester,
                            'academic_year' => $currentAcademicYear,
                            'status' => 'enrolled',
                            'enrollment_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                            'grade' => null, // Will be assigned when course is completed
                            'remarks' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        // Update course current_students count
                        $course->increment('current_students');
                    }
                }
            }
        }
        
        // Insert enrollments in batches
        $chunks = array_chunk($enrollments, 500);
        foreach ($chunks as $chunk) {
            DB::table('course_enrollments')->insert($chunk);
        }
        
        $this->command->info('Created ' . count($enrollments) . ' course enrollments');
        
        // Update course current_students counts
        foreach ($courses as $course) {
            $actualCount = CourseEnrollment::where('course_id', $course->id)
                                         ->where('status', 'enrolled')
                                         ->count();
            $course->update(['current_students' => $actualCount]);
        }
        
        $this->command->info('Updated course enrollment counts');
        
        // Assign some grades for completed courses (for upperclassmen)
        $this->assignGradesForCompletedCourses();
    }
    
    private function getStudentYearLevel($student): string
    {
        // Use student's current_year or year_level field
        if ($student->current_year) {
            return match($student->current_year) {
                1 => '1st',
                2 => '2nd', 
                3 => '3rd',
                4 => '4th',
                default => '1st'
            };
        }
        
        // Fallback to year_level field
        if ($student->year_level) {
            return $student->year_level;
        }
        
        // Random assignment for demo purposes
        $yearLevels = ['1st', '2nd', '3rd', '4th'];
        return $yearLevels[array_rand($yearLevels)];
    }
    
    private function assignGradesForCompletedCourses(): void
    {
        $this->command->info('Assigning grades for completed courses...');
        
        // Get some enrollments for upperclassmen to mark as completed
        $completedEnrollments = CourseEnrollment::where('status', 'enrolled')
                                              ->whereHas('student', function ($query) {
                                                  $query->whereIn('current_year', [3, 4])
                                                        ->orWhereIn('year_level', ['3rd', '4th']);
                                              })
                                              ->where('semester', 'First Semester')
                                              ->take(200) // Complete some courses
                                              ->get();
        
        foreach ($completedEnrollments as $enrollment) {
            // Assign realistic grades
            $grade = $this->generateRealisticGrade();
            
            $enrollment->update([
                'grade' => $grade,
                'status' => $grade >= 75 ? 'completed' : 'failed',
                'remarks' => $grade >= 75 ? 'Successfully completed' : 'Needs to retake'
            ]);
        }
        
        $this->command->info('Assigned grades to ' . $completedEnrollments->count() . ' completed courses');
    }
    
    private function generateRealisticGrade(): float
    {
        // Generate realistic grade distribution
        $rand = mt_rand(1, 100);
        
        if ($rand <= 5) return mt_rand(90, 98) / 1.0;      // 5% excellent (90-98)
        if ($rand <= 20) return mt_rand(85, 89) / 1.0;     // 15% very good (85-89)
        if ($rand <= 50) return mt_rand(78, 84) / 1.0;     // 30% good (78-84)
        if ($rand <= 75) return mt_rand(75, 77) / 1.0;     // 25% passing (75-77)
        if ($rand <= 90) return mt_rand(70, 74) / 1.0;     // 15% failing (70-74)
        return mt_rand(65, 69) / 1.0;                      // 10% poor (65-69)
    }
}
