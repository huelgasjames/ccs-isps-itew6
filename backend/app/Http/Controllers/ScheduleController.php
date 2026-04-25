<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::with(['course:id,course_code,course_name', 'professor:id,first_name,last_name,email']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('section', 'like', "%{$search}%")
                    ->orWhere('room', 'like', "%{$search}%")
                    ->orWhere('day_of_week', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        return response()->json($query->orderBy('day_of_week')->orderBy('start_time')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:professors,id',
            'section' => 'required|string|max:20',
            'room' => 'required|string|max:50',
            'room_type' => 'required|in:lecture,lab,computer_lab,multimedia',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|in:First Semester,Second Semester,Summer',
            'max_capacity' => 'required|integer|min:1|max:300',
            'current_enrollment' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['current_enrollment'] = $validated['current_enrollment'] ?? 0;

        $schedule = Schedule::create($validated)->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name,email']);
        return response()->json($schedule, 201);
    }

    public function show(Schedule $schedule): JsonResponse
    {
        return response()->json(
            $schedule->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name,email'])
        );
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:professors,id',
            'section' => 'required|string|max:20',
            'room' => 'required|string|max:50',
            'room_type' => 'required|in:lecture,lab,computer_lab,multimedia',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|in:First Semester,Second Semester,Summer',
            'max_capacity' => 'required|integer|min:1|max:300',
            'current_enrollment' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['current_enrollment'] = $validated['current_enrollment'] ?? 0;
        $schedule->update($validated);

        return response()->json(
            $schedule->load(['course:id,course_code,course_name', 'professor:id,first_name,last_name,email'])
        );
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();
        return response()->json(null, 204);
    }
}
