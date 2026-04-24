<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use App\Models\Course;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SyllabusController extends Controller
{
    /**
     * Display a listing of syllabi.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Syllabus::with(['course:id,course_code,course_name', 'professor:id,first_name,last_name', 'approvedBy:id,name']);

        // Filter by course
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by professor
        if ($request->has('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        // Filter by academic year
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by approval status
        if ($request->has('approved')) {
            $query->where('approved', $request->boolean('approved'));
        }

        $syllabi = $query->orderBy('academic_year', 'desc')
                         ->orderBy('semester', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json($syllabi);
    }

    /**
     * Store a newly created syllabus.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:professors,id',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|string|in:First Semester,Second Semester,Summer',
            'course_description' => 'required|string',
            'learning_objectives' => 'required|string',
            'topics_outline' => 'required|string',
            'assessment_methods' => 'required|string',
            'grading_policy' => 'required|string',
            'required_materials' => 'required|string',
            'class_policies' => 'required|string',
            'file_path' => 'nullable|string',
            'approved' => 'boolean',
        ]);

        // Check if syllabus already exists for this course, professor, academic year, and semester
        $existingSyllabus = Syllabus::where('course_id', $request->course_id)
                                    ->where('professor_id', $request->professor_id)
                                    ->where('academic_year', $request->academic_year)
                                    ->where('semester', $request->semester)
                                    ->first();

        if ($existingSyllabus) {
            return response()->json([
                'message' => 'Syllabus already exists for this course, professor, academic year, and semester',
                'existing_syllabus' => $existingSyllabus
            ], 422);
        }

        $syllabus = Syllabus::create($request->all());

        return response()->json([
            'message' => 'Syllabus created successfully',
            'syllabus' => $syllabus->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name', 'approvedBy:id,name'])
        ], 201);
    }

    /**
     * Display the specified syllabus.
     */
    public function show(Syllabus $syllabus): JsonResponse
    {
        return response()->json($syllabus->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name', 'approvedBy:id,name']));
    }

    /**
     * Update the specified syllabus.
     */
    public function update(Request $request, Syllabus $syllabus): JsonResponse
    {
        $request->validate([
            'course_id' => 'sometimes|required|exists:courses,id',
            'professor_id' => 'sometimes|required|exists:professors,id',
            'academic_year' => 'sometimes|required|string|max:20',
            'semester' => 'sometimes|required|string|in:First Semester,Second Semester,Summer',
            'course_description' => 'sometimes|required|string',
            'learning_objectives' => 'sometimes|required|string',
            'topics_outline' => 'sometimes|required|string',
            'assessment_methods' => 'sometimes|required|string',
            'grading_policy' => 'sometimes|required|string',
            'required_materials' => 'sometimes|required|string',
            'class_policies' => 'sometimes|required|string',
            'file_path' => 'nullable|string',
            'approved' => 'boolean',
        ]);

        $syllabus->update($request->all());

        return response()->json([
            'message' => 'Syllabus updated successfully',
            'syllabus' => $syllabus->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name', 'approvedBy:id,name'])
        ]);
    }

    /**
     * Remove the specified syllabus.
     */
    public function destroy(Syllabus $syllabus): JsonResponse
    {
        // Delete file if exists
        if ($syllabus->file_path && Storage::exists($syllabus->file_path)) {
            Storage::delete($syllabus->file_path);
        }

        $syllabus->delete();

        return response()->json([
            'message' => 'Syllabus deleted successfully'
        ]);
    }

    /**
     * Approve a syllabus.
     */
    public function approve(Request $request, Syllabus $syllabus): JsonResponse
    {
        $syllabus->update([
            'approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Syllabus approved successfully',
            'syllabus' => $syllabus->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name', 'approvedBy:id,name'])
        ]);
    }

    /**
     * Reject a syllabus.
     */
    public function reject(Syllabus $syllabus): JsonResponse
    {
        $syllabus->update([
            'approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return response()->json([
            'message' => 'Syllabus rejected successfully',
            'syllabus' => $syllabus->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name', 'approvedBy:id,name'])
        ]);
    }

    /**
     * Upload syllabus file.
     */
    public function uploadFile(Request $request, Syllabus $syllabus): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);

        // Delete old file if exists
        if ($syllabus->file_path && Storage::exists($syllabus->file_path)) {
            Storage::delete($syllabus->file_path);
        }

        $file = $request->file('file');
        $fileName = 'syllabi/' . $syllabus->course_id . '_' . $syllabus->professor_id . '_' . $syllabus->academic_year . '_' . $syllabus->semester . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        $filePath = $file->storeAs('syllabi', $fileName, 'public');

        $syllabus->update(['file_path' => $filePath]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file_path' => $filePath,
            'download_url' => Storage::url($filePath)
        ]);
    }

    /**
     * Download syllabus file.
     */
    public function downloadFile(Syllabus $syllabus): JsonResponse
    {
        if (!$syllabus->file_path || !Storage::exists($syllabus->file_path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return response()->json([
            'download_url' => Storage::url($syllabus->file_path),
            'file_name' => basename($syllabus->file_path)
        ]);
    }

    /**
     * Get syllabi for a specific course.
     */
    public function getByCourse(Request $request, Course $course): JsonResponse
    {
        $syllabi = $course->syllabi()
                         ->with(['professor:id,first_name,last_name', 'approvedBy:id,name'])
                         ->orderBy('academic_year', 'desc')
                         ->orderBy('semester', 'desc')
                         ->get();

        return response()->json($syllabi);
    }

    /**
     * Get syllabi for a specific professor.
     */
    public function getByProfessor(Request $request, Professor $professor): JsonResponse
    {
        $syllabi = $professor->syllabi()
                            ->with(['course:id,course_code,course_name', 'approvedBy:id,name'])
                            ->orderBy('academic_year', 'desc')
                            ->orderBy('semester', 'desc')
                            ->get();

        return response()->json($syllabi);
    }

    /**
     * Generate sample syllabi data for testing.
     */
    public function generateSampleData(): JsonResponse
    {
        $courses = Course::take(5)->get();
        $professors = Professor::take(3)->get();
        
        $sampleSyllabi = [];
        
        foreach ($courses as $course) {
            foreach ($professors as $professor) {
                foreach (['2024-2025', '2023-2024'] as $academicYear) {
                    foreach (['First Semester', 'Second Semester'] as $semester) {
                        $existingSyllabus = Syllabus::where('course_id', $course->id)
                                                    ->where('professor_id', $professor->id)
                                                    ->where('academic_year', $academicYear)
                                                    ->where('semester', $semester)
                                                    ->first();

                        if (!$existingSyllabus) {
                            $syllabus = Syllabus::create([
                                'course_id' => $course->id,
                                'professor_id' => $professor->id,
                                'academic_year' => $academicYear,
                                'semester' => $semester,
                                'course_description' => "This course provides an introduction to {$course->course_name}. Students will learn fundamental concepts and practical applications.",
                                'learning_objectives' => "• Understand core concepts\n• Develop practical skills\n• Apply theoretical knowledge\n• Complete hands-on projects",
                                'topics_outline' => "Week 1-2: Introduction\nWeek 3-4: Basic Concepts\nWeek 5-6: Advanced Topics\nWeek 7-8: Practical Applications\nWeek 9-10: Final Projects",
                                'assessment_methods' => "• Quizzes: 20%\n• Midterm Exam: 30%\n• Final Exam: 40%\n• Class Participation: 10%",
                                'grading_policy' => "A: 90-100%\nB: 80-89%\nC: 70-79%\nD: 60-69%\nF: Below 60%",
                                'required_materials' => "• Textbook: Main Course Text\n• Laptop with required software\n• Notebook and writing materials",
                                'class_policies' => "• Attendance is mandatory\n• Late submissions will be penalized\n• Academic integrity is strictly enforced",
                                'approved' => rand(0, 1) === 1,
                                'approved_at' => rand(0, 1) === 1 ? now() : null,
                                'approved_by' => rand(0, 1) === 1 ? 1 : null,
                            ]);
                            
                            $sampleSyllabi[] = $syllabus;
                        }
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Sample syllabi data generated successfully',
            'count' => count($sampleSyllabi),
            'syllabi' => $sampleSyllabi
        ]);
    }
}
