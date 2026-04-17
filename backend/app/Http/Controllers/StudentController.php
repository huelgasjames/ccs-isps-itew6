<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with([
            'user', 
            'skills', 
            'activities', 
            'academicHistory', 
            'affiliations', 
            'violations'
        ])
        ->active()
        ->get()
        ->map(function ($student) {
            // Add computed attributes
            $student->is_at_risk = $student->isAtRisk();
            $student->full_name = $student->full_name;
            
            // Transform to match frontend dummy data structure
            return [
                'id' => $student->id,
                'personalInfo' => $student->personal_info,
                'academicHistory' => $student->academicHistory,
                'academicStanding' => $student->academic_standing,
                'activities' => $student->activities,
                'violations' => $student->violations,
                'skills' => $student->skills,
                'affiliations' => $student->affiliations,
                'createdAt' => $student->created_at->toISOString(),
                'updatedAt' => $student->updated_at->toISOString(),
                'isActive' => $student->is_active,
            ];
        });

        return response()->json([
            'students' => $students,
            'total' => $students->count()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'age' => 'required|integer|min:16|max:100',
            'blood_type' => 'nullable|string|max:5',
            'disability_status' => 'boolean',
            'disability_name' => 'nullable|string|max:255',
            'scholar' => 'boolean',
            'working_student' => 'boolean',
            'email' => 'required|email|unique:students,email',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:50',
            'year_level' => 'nullable|in:1st,2nd,3rd,4th',
            'program' => 'required|in:BSIT,BSCS',
            'curriculum' => 'nullable|string|max:50',
            'academic_status' => 'nullable|in:Regular,Probation,Suspended,Graduated',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        $student = Student::create([
            'student_unique_id' => 'STU-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'blood_type' => $request->blood_type,
            'disability_status' => $request->disability_status ?? false,
            'disability_name' => $request->disability_name,
            'scholar' => $request->scholar ?? false,
            'working_student' => $request->working_student ?? false,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'section' => $request->section,
            'year_level' => $request->year_level,
            'program' => $request->program,
            'curriculum' => $request->curriculum,
            'academic_status' => $request->academic_status ?? 'Regular',
        ]);

        return response()->json($student->load('user'), 201);
    }

    public function show(Student $student)
    {
        $student->load(['user', 'skills', 'talents', 'sports', 'certificates', 'violations', 'organizations']);
        $student->is_at_risk = $student->isAtRisk();
        $student->full_name = $student->full_name;

        return response()->json($student);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'age' => 'required|integer|min:16|max:100',
            'blood_type' => 'nullable|string|max:5',
            'disability_status' => 'boolean',
            'disability_name' => 'nullable|string|max:255',
            'scholar' => 'boolean',
            'working_student' => 'boolean',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:50',
            'year_level' => 'nullable|in:1st,2nd,3rd,4th',
            'program' => 'required|in:BSIT,BSCS',
            'curriculum' => 'nullable|string|max:50',
            'academic_status' => 'nullable|in:Regular,Probation,Suspended,Graduated',
        ]);

        $student->update($request->all());

        if ($student->user) {
            $student->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);
        }

        return response()->json($student->load('user'));
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json(null, 204);
    }

    public function getAtRiskStudents()
    {
        $students = Student::with(['user', 'violations'])
            ->get()
            ->filter(function ($student) {
                return $student->isAtRisk();
            })
            ->values();

        return response()->json($students);
    }
}
