<?php

namespace App\Http\Controllers;

use App\Models\StudentActivity;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    public function index()
    {
        $activities = StudentActivity::with('student.user')->get();
        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:organization,sports,research,volunteer,club',
            'role' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
            'achievements' => 'nullable|array',
            'level' => 'nullable|in:local,regional,national,international',
        ]);

        $activity = StudentActivity::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'type' => $request->type,
            'role' => $request->role,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'achievements' => $request->achievements,
            'level' => $request->level ?? 'local',
        ]);

        return response()->json($activity->load('student.user'), 201);
    }

    public function show(StudentActivity $activity)
    {
        return response()->json($activity->load('student.user'));
    }

    public function update(Request $request, StudentActivity $activity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:organization,sports,research,volunteer,club',
            'role' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
            'achievements' => 'nullable|array',
            'level' => 'nullable|in:local,regional,national,international',
        ]);

        $activity->update($request->all());
        return response()->json($activity->load('student.user'));
    }

    public function destroy(StudentActivity $activity)
    {
        $activity->delete();
        return response()->json(null, 204);
    }

    public function getStudentActivities(Student $student)
    {
        $activities = $student->activities()->with('student.user')->get();
        return response()->json($activities);
    }
}
