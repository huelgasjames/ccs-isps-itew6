<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfessorController extends Controller
{
    public function index()
    {
        $professors = Professor::with('user')->get()->map(function ($professor) {
            $professor->full_name = $professor->full_name;
            return $professor;
        });

        return response()->json($professors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'age' => 'required|integer|min:21|max:100',
            'birthday' => 'required|date',
            'blood_type' => 'nullable|string|max:5',
            'disability_status' => 'boolean',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|unique:professors,email',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'educational_attainment' => 'nullable|string|max:255',
            'experience' => 'nullable|string',
            'courses_handled' => 'nullable|string',
            'role' => 'nullable|string|max:100',
            'employment_type' => 'nullable|in:Full-time,Part-time,Contract',
            'department' => 'nullable|string|max:100',
            'organization' => 'nullable|string|max:100',
            'application_date' => 'nullable|date',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'professor',
        ]);

        $professor = Professor::create([
            'professor_unique_id' => 'PROF-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'birthday' => $request->birthday,
            'blood_type' => $request->blood_type,
            'disability_status' => $request->disability_status ?? false,
            'gender' => $request->gender,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'educational_attainment' => $request->educational_attainment,
            'experience' => $request->experience,
            'courses_handled' => $request->courses_handled,
            'role' => $request->role,
            'employment_type' => $request->employment_type,
            'department' => $request->department ?? 'CCS',
            'organization' => $request->organization,
            'application_date' => $request->application_date,
        ]);

        return response()->json($professor->load('user'), 201);
    }

    public function show(Professor $professor)
    {
        $professor->load('user');
        $professor->full_name = $professor->full_name;

        return response()->json($professor);
    }

    public function update(Request $request, Professor $professor)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'age' => 'required|integer|min:21|max:100',
            'birthday' => 'required|date',
            'blood_type' => 'nullable|string|max:5',
            'disability_status' => 'boolean',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|unique:professors,email,' . $professor->id,
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'educational_attainment' => 'nullable|string|max:255',
            'experience' => 'nullable|string',
            'courses_handled' => 'nullable|string',
            'role' => 'nullable|string|max:100',
            'employment_type' => 'nullable|in:Full-time,Part-time,Contract',
            'department' => 'nullable|string|max:100',
            'organization' => 'nullable|string|max:100',
            'application_date' => 'nullable|date',
        ]);

        $professor->update($request->all());

        if ($professor->user) {
            $professor->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);
        }

        return response()->json($professor->load('user'));
    }

    public function destroy(Professor $professor)
    {
        $professor->delete();

        return response()->json(null, 204);
    }

    public function generateSampleData(): JsonResponse
    {
        $samples = [
            [
                'first_name' => 'Dr. Roberto',
                'last_name' => 'Mendoza',
                'email' => 'roberto.mendoza@ccs.edu',
                'gender' => 'Male',
                'contact_number' => '09123456789',
                'address' => '123 University Ave, Manila',
                'educational_attainment' => 'PhD in Computer Science',
                'experience' => '15 years',
                'courses_handled' => 'Data Structures, Algorithms, Database Systems',
                'role' => 'Department Head',
                'employment_type' => 'Full-time',
                'department' => 'Computer Science',
                'organization' => 'CCS Faculty',
                'application_date' => '2010-06-15',
            ],
            [
                'first_name' => 'Prof. Cristina',
                'last_name' => 'Reyes',
                'email' => 'cristina.reyes@ccs.edu',
                'gender' => 'Female',
                'contact_number' => '09234567890',
                'address' => '456 College Rd, Quezon City',
                'educational_attainment' => 'MS in Information Technology',
                'experience' => '8 years',
                'courses_handled' => 'Web Development, Mobile Programming',
                'role' => 'Assistant Professor',
                'employment_type' => 'Full-time',
                'department' => 'Information Technology',
                'organization' => 'CCS Faculty',
                'application_date' => '2016-08-20',
            ],
            [
                'first_name' => 'Dr. Antonio',
                'last_name' => 'Santos',
                'email' => 'antonio.santos@ccs.edu',
                'gender' => 'Male',
                'contact_number' => '09345678901',
                'address' => '789 Academic St, Makati',
                'educational_attainment' => 'PhD in Computer Engineering',
                'experience' => '12 years',
                'courses_handled' => 'Computer Networks, Security, Cloud Computing',
                'role' => 'Associate Professor',
                'employment_type' => 'Full-time',
                'department' => 'Computer Engineering',
                'organization' => 'CCS Research Group',
                'application_date' => '2012-03-10',
            ],
        ];

        $created = [];
        foreach ($samples as $data) {
            $existing = Professor::where('email', $data['email'])->first();
            if (!$existing) {
                $created[] = Professor::create($data);
            }
        }

        return response()->json([
            'message' => count($created) . ' sample professors generated',
            'professors' => $created,
        ]);
    }
}
