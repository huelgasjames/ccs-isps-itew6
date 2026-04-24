<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query();

        // Search functionality
        if ($request->search) {
            $query->search($request->search);
        }

        // Filters
        if ($request->department) {
            $query->byDepartment($request->department);
        }

        if ($request->semester) {
            $query->bySemester($request->semester);
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            } elseif ($request->status === 'archived') {
                $query->archived();
            }
        }

        if ($request->academic_year) {
            $query->byAcademicYear($request->academic_year);
        }

        $courses = $query->with(['syllabi' => function ($q) {
            $q->where('academic_year', now()->year)
              ->where('semester', $this->getCurrentSemester());
        }])->get();

        return response()->json($courses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'courseCode' => 'required|string|max:20|unique:courses,course_code',
            'courseName' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:12',
            'department' => 'required|string',
            'semester' => 'required|in:First Semester,Second Semester,Summer',
            'academicYear' => 'required|string',
            'instructor' => 'nullable|string',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string',
            'maxStudents' => 'required|integer|min:1|max:40',
            'status' => 'required|in:active,inactive,archived',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string'
        ]);

        // Convert frontend field names to database field names
        $courseData = [
            'course_code' => $validated['courseCode'],
            'course_name' => $validated['courseName'],
            'description' => $validated['description'],
            'credits' => $validated['credits'],
            'department' => $validated['department'],
            'semester' => $validated['semester'],
            'academic_year' => $validated['academicYear'],
            'instructor' => $validated['instructor'],
            'schedule' => $validated['schedule'],
            'room' => $validated['room'],
            'max_students' => $validated['maxStudents'],
            'status' => $validated['status'],
            'prerequisites' => $validated['prerequisites'] ?? [],
        ];

        $course = Course::create($courseData);

        return response()->json($course, 201);
    }

    public function show(Course $course): JsonResponse
    {
        $course->load(['syllabi.professor', 'schedules.professor']);
        return response()->json($course);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'courseCode' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'courseName' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:12',
            'department' => 'required|string',
            'semester' => 'required|in:First Semester,Second Semester,Summer',
            'academicYear' => 'required|string',
            'instructor' => 'nullable|string',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string',
            'maxStudents' => 'required|integer|min:1|max:40',
            'status' => 'required|in:active,inactive,archived',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string'
        ]);

        // Convert frontend field names to database field names
        $courseData = [
            'course_code' => $validated['courseCode'],
            'course_name' => $validated['courseName'],
            'description' => $validated['description'],
            'credits' => $validated['credits'],
            'department' => $validated['department'],
            'semester' => $validated['semester'],
            'academic_year' => $validated['academicYear'],
            'instructor' => $validated['instructor'],
            'schedule' => $validated['schedule'],
            'room' => $validated['room'],
            'max_students' => $validated['maxStudents'],
            'status' => $validated['status'],
            'prerequisites' => $validated['prerequisites'] ?? [],
        ];

        $course->update($courseData);

        return response()->json($course);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();
        return response()->json(null, 204);
    }

    public function archive(Course $course): JsonResponse
    {
        $course->update(['status' => 'archived']);
        return response()->json($course);
    }

    public function analytics(): JsonResponse
    {
        $totalCourses = Course::count();
        $activeCourses = Course::active()->count();
        $inactiveCourses = Course::inactive()->count();
        $archivedCourses = Course::archived()->count();
        
        $totalDepartments = Course::distinct('department')->count('department');
        $totalStudents = Course::sum('current_students');
        $averageCredits = Course::avg('credits');

        // Courses by department
        $coursesByDepartment = Course::select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) use ($totalCourses) {
                return [
                    'department' => $item->department,
                    'count' => $item->count,
                    'percentage' => $totalCourses > 0 ? round(($item->count / $totalCourses) * 100, 1) : 0
                ];
            });

        // Courses by semester
        $coursesBySemester = Course::select('semester', DB::raw('count(*) as count'))
            ->groupBy('semester')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) use ($totalCourses) {
                return [
                    'semester' => $item->semester,
                    'count' => $item->count,
                    'percentage' => $totalCourses > 0 ? round(($item->count / $totalCourses) * 100, 1) : 0
                ];
            });

        return response()->json([
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'inactiveCourses' => $inactiveCourses,
            'archivedCourses' => $archivedCourses,
            'totalDepartments' => $totalDepartments,
            'averageCredits' => round($averageCredits, 1),
            'totalStudents' => $totalStudents,
            'coursesByDepartment' => $coursesByDepartment,
            'coursesBySemester' => $coursesBySemester
        ]);
    }

    public function generateSampleData(): JsonResponse
    {
        $sampleCourses = [
            [
                'course_code' => 'CCS 101',
                'course_name' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts of computer science and programming',
                'credits' => 3,
                'department' => 'Computer Science',
                'semester' => 'First Semester',
                'academic_year' => '2024-2025',
                'instructor' => 'Dr. John Smith',
                'schedule' => 'MWF 9:00-10:30 AM',
                'room' => 'Room 101',
                'max_students' => 40,
                'current_students' => 35,
                'status' => 'active',
                'prerequisites' => []
            ],
            [
                'course_code' => 'CCS 102',
                'course_name' => 'Programming Fundamentals',
                'description' => 'Introduction to programming concepts and problem solving',
                'credits' => 3,
                'department' => 'Computer Science',
                'semester' => 'First Semester',
                'academic_year' => '2024-2025',
                'instructor' => 'Prof. Jane Doe',
                'schedule' => 'TTH 1:00-2:30 PM',
                'room' => 'Room 102',
                'max_students' => 35,
                'current_students' => 32,
                'status' => 'active',
                'prerequisites' => ['CCS 101']
            ],
            [
                'course_code' => 'CCS 103',
                'course_name' => 'Data Structures and Algorithms',
                'description' => 'Advanced data structures and algorithm analysis',
                'credits' => 4,
                'department' => 'Computer Science',
                'semester' => 'Second Semester',
                'academic_year' => '2024-2025',
                'instructor' => 'Dr. Robert Johnson',
                'schedule' => 'MWF 2:00-3:30 PM',
                'room' => 'Room 103',
                'max_students' => 30,
                'current_students' => 28,
                'status' => 'active',
                'prerequisites' => ['CCS 102']
            ]
        ];

        // Generate CCS 104-112
        $courseNames = [
            'Web Development Fundamentals',
            'Database Management Systems',
            'Software Engineering',
            'Computer Networks',
            'Operating Systems',
            'Artificial Intelligence',
            'Machine Learning',
            'Cybersecurity Fundamentals',
            'Mobile Application Development'
        ];

        for ($i = 4; $i <= 12; $i++) {
            $sampleCourses[] = [
                'course_code' => "CCS " . (100 + $i),
                'course_name' => $courseNames[$i - 4],
                'description' => "Advanced topics in computer science focusing on " . strtolower($courseNames[$i - 4]),
                'credits' => 3 + ($i % 2),
                'department' => 'Computer Science',
                'semester' => $i % 2 === 0 ? 'First Semester' : 'Second Semester',
                'academic_year' => '2024-2025',
                'instructor' => "Prof. Instructor {$i}",
                'schedule' => $i % 2 === 0 ? 'TTH 9:00-10:30 AM' : 'MWF 1:00-2:30 PM',
                'room' => "Room " . (100 + $i),
                'max_students' => 30 + ($i % 3) * 5,
                'current_students' => 25 + ($i % 4) * 3,
                'status' => $i <= 8 ? 'active' : 'inactive',
                'prerequisites' => $i > 1 ? ["CCS " . (99 + $i)] : []
            ];
        }

        // Clear existing courses and insert sample data
        Course::truncate();
        Course::insert($sampleCourses);

        return response()->json(['message' => 'Sample data generated successfully']);
    }

    private function getCurrentSemester(): string
    {
        $month = now()->month;
        return ($month >= 6 && $month <= 10) ? 'First Semester' : 'Second Semester';
    }

    /**
     * Get enrollments for a specific course
     */
    public function enrollments(Request $request, Course $course): JsonResponse
    {
        $enrollments = $course->courseEnrollments()
            ->with('student:id,first_name,last_name,student_id,email')
            ->when($request->semester, function ($query, $semester) {
                return $query->where('semester', $semester);
            })
            ->when($request->academic_year, function ($query, $academicYear) {
                return $query->where('academic_year', $academicYear);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Get enrollments for a specific student
     */
    public function studentEnrollments(Request $request, Student $student): JsonResponse
    {
        $enrollments = $student->courseEnrollments()
            ->with('course:id,course_code,course_name,credits,instructor')
            ->when($request->semester, function ($query, $semester) {
                return $query->where('semester', $semester);
            })
            ->when($request->academic_year, function ($query, $academicYear) {
                return $query->where('academic_year', $academicYear);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Enroll a student in a course
     */
    public function enrollStudent(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
        ]);

        $studentId = $request->student_id;
        $semester = $request->semester;
        $academicYear = $request->academic_year;

        // Check if already enrolled
        $existingEnrollment = \App\Models\CourseEnrollment::where('student_id', $studentId)
            ->where('course_id', $course->id)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'Student is already enrolled in this course',
                'enrollment' => $existingEnrollment
            ], 422);
        }

        // Check course capacity (enforce 40-student maximum)
        $currentEnrollments = \App\Models\CourseEnrollment::where('course_id', $course->id)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->where('status', 'enrolled')
            ->count();

        $maxCapacity = min($course->max_students, 40); // Enforce 40-student maximum

        if ($currentEnrollments >= $maxCapacity) {
            return response()->json([
                'message' => 'Course is full - Maximum 40 students allowed per section',
                'current_enrollments' => $currentEnrollments,
                'max_capacity' => $maxCapacity
            ], 422);
        }

        // Create enrollment
        $enrollment = \App\Models\CourseEnrollment::create([
            'student_id' => $studentId,
            'course_id' => $course->id,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        // Update course current_students count
        $course->increment('current_students');

        return response()->json([
            'message' => 'Student enrolled successfully',
            'enrollment' => $enrollment->load('student:id,first_name,last_name,student_id')
        ]);
    }

    /**
     * Drop a student from a course
     */
    public function dropCourse(\App\Models\CourseEnrollment $enrollment): JsonResponse
    {
        $course = $enrollment->course;
        
        // Update enrollment status
        $enrollment->update(['status' => 'dropped']);
        
        // Update course current_students count
        $course->decrement('current_students');
        
        return response()->json([
            'message' => 'Student dropped from course successfully',
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Assign grade to an enrollment
     */
    public function assignGrade(Request $request, \App\Models\CourseEnrollment $enrollment): JsonResponse
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string'
        ]);

        $grade = $request->grade;
        $status = $grade >= 75 ? 'completed' : 'failed';
        $remarks = $request->remarks ?: ($grade >= 75 ? 'Successfully completed' : 'Needs to retake');

        $enrollment->update([
            'grade' => $grade,
            'status' => $status,
            'remarks' => $remarks
        ]);

        return response()->json([
            'message' => 'Grade assigned successfully',
            'enrollment' => $enrollment->load('student:id,first_name,last_name', 'course:id,course_code,course_name')
        ]);
    }

    /**
     * Update enrollment details
     */
    public function updateEnrollment(Request $request, \App\Models\CourseEnrollment $enrollment): JsonResponse
    {
        $request->validate([
            'grade' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:enrolled,dropped,completed,failed',
            'remarks' => 'nullable|string'
        ]);

        $data = $request->only(['grade', 'status', 'remarks']);
        
        // Auto-determine status based on grade if grade is provided but status is not
        if (isset($data['grade']) && !isset($data['status'])) {
            $data['status'] = $data['grade'] >= 75 ? 'completed' : 'failed';
        }

        $enrollment->update($data);

        return response()->json([
            'message' => 'Enrollment updated successfully',
            'enrollment' => $enrollment->load('student:id,first_name,last_name,student_id', 'course:id,course_code,course_name')
        ]);
    }

    /**
     * Bulk enroll students in a course
     */
    public function bulkEnroll(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string'
        ]);

        $studentIds = $request->student_ids;
        $semester = $request->semester;
        $academicYear = $request->academic_year;

        // Check course capacity
        $currentEnrollments = \App\Models\CourseEnrollment::where('course_id', $course->id)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->where('status', 'enrolled')
            ->count();

        $maxCapacity = min($course->max_students, 40);
        $availableSlots = $maxCapacity - $currentEnrollments;
        $studentsToEnroll = min(count($studentIds), $availableSlots);

        if ($studentsToEnroll === 0) {
            return response()->json([
                'message' => 'Course is full - No available slots',
                'current_enrollments' => $currentEnrollments,
                'max_capacity' => $maxCapacity
            ], 422);
        }

        $enrolledStudents = [];
        $failedStudents = [];

        foreach (array_slice($studentIds, 0, $studentsToEnroll) as $studentId) {
            // Check if already enrolled
            $existingEnrollment = \App\Models\CourseEnrollment::where('student_id', $studentId)
                ->where('course_id', $course->id)
                ->where('semester', $semester)
                ->where('academic_year', $academicYear)
                ->first();

            if ($existingEnrollment) {
                $failedStudents[] = [
                    'student_id' => $studentId,
                    'reason' => 'Already enrolled'
                ];
                continue;
            }

            try {
                $enrollment = \App\Models\CourseEnrollment::create([
                    'student_id' => $studentId,
                    'course_id' => $course->id,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                    'status' => 'enrolled',
                    'enrollment_date' => now(),
                ]);

                $enrolledStudents[] = $enrollment->load('student:id,first_name,last_name,student_id');
            } catch (\Exception $e) {
                $failedStudents[] = [
                    'student_id' => $studentId,
                    'reason' => $e->getMessage()
                ];
            }
        }

        // Update course current_students count
        $course->increment('current_students', count($enrolledStudents));

        return response()->json([
            'message' => "Successfully enrolled " . count($enrolledStudents) . " students",
            'enrolled_students' => $enrolledStudents,
            'failed_students' => $failedStudents,
            'course_capacity' => [
                'current' => $currentEnrollments + count($enrolledStudents),
                'max' => $maxCapacity
            ]
        ]);
    }

    /**
     * Bulk drop students from a course
     */
    public function bulkDrop(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'required|exists:course_enrollments,id'
        ]);

        $enrollmentIds = $request->enrollment_ids;
        $droppedCount = 0;

        foreach ($enrollmentIds as $enrollmentId) {
            $enrollment = \App\Models\CourseEnrollment::find($enrollmentId);
            if ($enrollment && $enrollment->course_id === $course->id) {
                $enrollment->update(['status' => 'dropped']);
                $droppedCount++;
            }
        }

        // Update course current_students count
        $course->decrement('current_students', $droppedCount);

        return response()->json([
            'message' => "Successfully dropped {$droppedCount} students from course",
            'dropped_count' => $droppedCount
        ]);
    }

    /**
     * Get enrollment statistics for a course
     */
    public function enrollmentStats(Request $request, Course $course): JsonResponse
    {
        $semester = $request->semester;
        $academicYear = $request->academic_year;

        $query = $course->courseEnrollments();
        
        if ($semester) {
            $query->where('semester', $semester);
        }
        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        $enrollments = $query->get();

        $stats = [
            'total_enrollments' => $enrollments->count(),
            'enrolled' => $enrollments->where('status', 'enrolled')->count(),
            'completed' => $enrollments->where('status', 'completed')->count(),
            'failed' => $enrollments->where('status', 'failed')->count(),
            'dropped' => $enrollments->where('status', 'dropped')->count(),
            'average_grade' => $enrollments->whereNotNull('grade')->avg('grade'),
            'course_capacity' => [
                'current_students' => $course->current_students,
                'max_students' => $course->max_students,
                'available_slots' => $course->available_slots,
                'enrollment_percentage' => $course->enrollment_percentage
            ]
        ];

        return response()->json($stats);
    }
}
