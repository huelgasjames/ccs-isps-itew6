<?php

namespace App\Http\Controllers;

use App\Models\StudentAcademicHistory;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentAcademicHistoryController extends Controller
{
    public function index()
    {
        $histories = StudentAcademicHistory::with('student.user')->get();
        return response()->json($histories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'school_name' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'gpa' => 'nullable|decimal:0,2',
            'honors' => 'nullable|array',
            'status' => 'nullable|in:completed,ongoing,transferred',
        ]);

        $history = StudentAcademicHistory::create([
            'student_id' => $request->student_id,
            'school_name' => $request->school_name,
            'degree' => $request->degree,
            'major' => $request->major,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'gpa' => $request->gpa,
            'honors' => $request->honors,
            'status' => $request->status ?? 'completed',
        ]);

        return response()->json($history->load('student.user'), 201);
    }

    public function show(StudentAcademicHistory $history)
    {
        return response()->json($history->load('student.user'));
    }

    public function update(Request $request, StudentAcademicHistory $history)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'gpa' => 'nullable|decimal:0,2',
            'honors' => 'nullable|array',
            'status' => 'nullable|in:completed,ongoing,transferred',
        ]);

        $history->update($request->all());
        return response()->json($history->load('student.user'));
    }

    public function destroy(StudentAcademicHistory $history)
    {
        $history->delete();
        return response()->json(null, 204);
    }

    public function getStudentHistory(Student $student)
    {
        $histories = $student->academicHistory()->with('student.user')->get();
        return response()->json($histories);
    }
}
