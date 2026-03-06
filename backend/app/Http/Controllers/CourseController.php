<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('course_code', 'like', '%' . $request->search . '%')
                  ->orWhere('course_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->level) {
            $query->where('level', $request->level);
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
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
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:12',
            'department' => 'required|string',
            'level' => 'required|in:1st,2nd,3rd,4th,5th',
            'semester' => 'required|in:1st,2nd,summer',
            'prerequisites' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $course = Course::create($validated);

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
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:12',
            'department' => 'required|string',
            'level' => 'required|in:1st,2nd,3rd,4th,5th',
            'semester' => 'required|in:1st,2nd,summer',
            'prerequisites' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $course->update($validated);

        return response()->json($course);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();
        return response()->json(null, 204);
    }

    private function getCurrentSemester(): string
    {
        $month = now()->month;
        return ($month >= 6 && $month <= 10) ? '1st' : '2nd';
    }
}
