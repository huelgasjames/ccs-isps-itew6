<?php

namespace App\Http\Controllers;

use App\Models\StudentAffiliation;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentAffiliationController extends Controller
{
    public function index()
    {
        $affiliations = StudentAffiliation::with('student.user')->get();
        return response()->json($affiliations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:student_organization,department,professional,community',
            'role' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'position' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $affiliation = StudentAffiliation::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'type' => $request->type,
            'role' => $request->role,
            'start_date' => $request->start_date,
            'position' => $request->position,
            'description' => $request->description,
        ]);

        return response()->json($affiliation->load('student.user'), 201);
    }

    public function show(StudentAffiliation $affiliation)
    {
        return response()->json($affiliation->load('student.user'));
    }

    public function update(Request $request, StudentAffiliation $affiliation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:student_organization,department,professional,community',
            'role' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'position' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $affiliation->update($request->all());
        return response()->json($affiliation->load('student.user'));
    }

    public function destroy(StudentAffiliation $affiliation)
    {
        $affiliation->delete();
        return response()->json(null, 204);
    }

    public function getStudentAffiliations(Student $student)
    {
        $affiliations = $student->affiliations()->get();
        return response()->json($affiliations);
    }
}
