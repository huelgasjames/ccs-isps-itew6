<?php

namespace App\Http\Controllers;

use App\Models\StudentViolation;
use App\Models\Student;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index()
    {
        $violations = StudentViolation::with('student.user')->get();
        return response()->json($violations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:academic,disciplinary,attendance,conduct',
            'description' => 'required|string',
            'date' => 'required|date',
            'severity' => 'required|in:minor,moderate,major,severe',
            'status' => 'nullable|in:pending,resolved,appealed',
            'consequence' => 'nullable|string|max:255',
        ]);

        $violation = StudentViolation::create([
            'student_id' => $request->student_id,
            'type' => $request->type,
            'description' => $request->description,
            'date' => $request->date,
            'severity' => $request->severity,
            'status' => $request->status ?? 'pending',
            'consequence' => $request->consequence,
        ]);

        return response()->json($violation->load('student.user'), 201);
    }

    public function show(StudentViolation $violation)
    {
        return response()->json($violation->load('student.user'));
    }

    public function update(Request $request, StudentViolation $violation)
    {
        $request->validate([
            'type' => 'required|in:academic,disciplinary,attendance,conduct',
            'description' => 'required|string',
            'date' => 'required|date',
            'severity' => 'required|in:minor,moderate,major,severe',
            'status' => 'nullable|in:pending,resolved,appealed',
            'consequence' => 'nullable|string|max:255',
        ]);

        $violation->update($request->all());

        return response()->json($violation->load('student.user'));
    }

    public function destroy(StudentViolation $violation)
    {
        $violation->delete();

        return response()->json(null, 204);
    }

    public function resolveViolation(StudentViolation $violation)
    {
        $violation->update(['status' => 'resolved']);

        return response()->json($violation->load('student.user'));
    }

    public function getStudentViolations(Student $student)
    {
        $violations = $student->violations()->with('student.user')->get();
        
        return response()->json($violations);
    }

    public function getPendingViolations()
    {
        $violations = StudentViolation::with('student.user')
            ->where('status', 'pending')
            ->get();

        return response()->json($violations);
    }
}
