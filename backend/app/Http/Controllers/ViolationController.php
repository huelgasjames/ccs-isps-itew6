<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Student;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index()
    {
        $violations = Violation::with('student.user')->get();
        return response()->json($violations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sanction' => 'nullable|string|max:255',
            'date_committed' => 'required|date',
            'status' => 'nullable|in:Pending,Resolved',
        ]);

        $violation = Violation::create([
            'student_id' => $request->student_id,
            'violation_type' => $request->violation_type,
            'description' => $request->description,
            'sanction' => $request->sanction,
            'date_committed' => $request->date_committed,
            'status' => $request->status ?? 'Pending',
        ]);

        return response()->json($violation->load('student.user'), 201);
    }

    public function show(Violation $violation)
    {
        return response()->json($violation->load('student.user'));
    }

    public function update(Request $request, Violation $violation)
    {
        $request->validate([
            'violation_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sanction' => 'nullable|string|max:255',
            'date_committed' => 'required|date',
            'status' => 'nullable|in:Pending,Resolved',
        ]);

        $violation->update($request->all());

        return response()->json($violation->load('student.user'));
    }

    public function destroy(Violation $violation)
    {
        $violation->delete();

        return response()->json(null, 204);
    }

    public function resolveViolation(Violation $violation)
    {
        $violation->update(['status' => 'Resolved']);

        return response()->json($violation->load('student.user'));
    }

    public function getStudentViolations(Student $student)
    {
        $violations = $student->violations()->with('student.user')->get();
        
        return response()->json($violations);
    }

    public function getPendingViolations()
    {
        $violations = Violation::with('student.user')
            ->where('status', 'Pending')
            ->get();

        return response()->json($violations);
    }
}
