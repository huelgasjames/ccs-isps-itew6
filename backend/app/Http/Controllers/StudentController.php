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
        try {
            $students = Student::get();
            
            \Log::info('Students found: ' . $students->count());
            
            $mapped = $students->map(function ($student) {
                // Add computed attributes
                $student->is_at_risk = $student->isAtRisk();
                $student->full_name = $student->full_name;
                
                // Return flat structure matching what StudentListView expects
                return [
                    'id' => $student->id,
                    'first_name' => $student->first_name,
                    'middle_name' => $student->middle_name,
                    'last_name' => $student->last_name,
                    'student_number' => $student->student_id,
                    'year_level' => $student->year_level ?? $student->current_year,
                    'academic_standing' => $student->standing,
                    'gpa' => $student->current_gpa,
                    'phone' => $student->phone,
                    'date_of_birth' => $student->date_of_birth,
                    'gender' => $student->gender,
                    'city' => $student->city,
                    'emergency_contact_name' => $student->emergency_contact_name,
                    'emergency_contact_phone' => $student->emergency_contact_phone,
                    'created_at' => $student->created_at,
                    'updated_at' => $student->updated_at,
                ];
            });

            return response()->json($mapped);
        } catch (\Exception $e) {
            \Log::error('Error in StudentController@index: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        // Check if it's the new form structure from StudentFormView
        if ($request->has('first_name') && $request->has('student_number')) {
            // Handle StudentFormView structure
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:100',
                    'middle_name' => 'nullable|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6',
                    'phone' => 'nullable|string|max:50',
                    'date_of_birth' => 'nullable|date',
                    'gender' => 'nullable|in:male,female,other',
                    'city' => 'nullable|string|max:100',
                    'student_number' => 'required|string|max:20|unique:students,student_id',
                    'year_level' => 'required|integer|min:1|max:4',
                    'academic_standing' => 'required|in:excellent,good,average,probation',
                    'gpa' => 'nullable|numeric|min:0|max:4',
                    'emergency_contact_name' => 'nullable|string|max:255',
                    'emergency_contact_phone' => 'nullable|string|max:50',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ], 422);
            }

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'city' => $request->city,
                'student_id' => $request->student_number,
                'current_year' => $request->year_level,
                'standing' => $request->academic_standing,
                'current_gpa' => $request->gpa,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);

            // Handle nested data (skills, activities, etc.)
            if ($request->has('skills') && is_array($request->skills)) {
                foreach ($request->skills as $skill) {
                    $student->skills()->create($skill);
                }
            }

            if ($request->has('activities') && is_array($request->activities)) {
                foreach ($request->activities as $activity) {
                    $student->activities()->create($activity);
                }
            }

            if ($request->has('academic_history') && is_array($request->academic_history)) {
                foreach ($request->academic_history as $history) {
                    $student->academicHistory()->create($history);
                }
            }

            if ($request->has('affiliations') && is_array($request->affiliations)) {
                foreach ($request->affiliations as $affiliation) {
                    $student->affiliations()->create($affiliation);
                }
            }

            if ($request->has('violations') && is_array($request->violations)) {
                foreach ($request->violations as $violation) {
                    $student->violations()->create($violation);
                }
            }

            return response()->json($student->load('user'), 201);
        }

        // Support both old flat structure and new nested structure from frontend
        $isNestedFormat = $request->has('personalInfo') || $request->has('academicStanding');

        if ($isNestedFormat) {
            // Handle nested format from StudentProfilingView.vue
            $request->validate([
                'personalInfo.firstName' => 'required|string|max:100',
                'personalInfo.lastName' => 'required|string|max:100',
                'personalInfo.middleName' => 'nullable|string|max:100',
                'personalInfo.studentId' => 'required|string|max:50',
                'personalInfo.email' => 'required|email|unique:students,email',
                'personalInfo.phone' => 'required|string|max:50',
                'personalInfo.dateOfBirth' => 'required|date',
                'personalInfo.age' => 'required|integer|min:16|max:100',
                'personalInfo.gender' => 'required|in:male,female,other',
                'personalInfo.address' => 'required|string|max:255',
                'personalInfo.city' => 'required|string|max:100',
                'personalInfo.province' => 'required|string|max:100',
                'personalInfo.postalCode' => 'required|string|max:20',
                'personalInfo.emergencyContact.name' => 'required|string|max:100',
                'personalInfo.emergencyContact.relationship' => 'required|string|max:50',
                'personalInfo.emergencyContact.phone' => 'required|string|max:50',
                'academicStanding.currentYear' => 'required|integer|min:1|max:4',
                'academicStanding.currentSemester' => 'required|in:first,second,summer',
                'academicStanding.currentGPA' => 'required|numeric|min:0|max:4',
                'academicStanding.totalUnits' => 'nullable|integer|min:0',
                'academicStanding.standing' => 'required|in:good,average,warning,probation',
                'academicStanding.advisor' => 'nullable|string|max:100',
            ]);

            $personalInfo = $request->input('personalInfo');
            $academicStanding = $request->input('academicStanding');

            // Generate a default password from student ID
            $defaultPassword = $personalInfo['studentId'] . 'CCS';

            $user = User::create([
                'name' => $personalInfo['firstName'] . ' ' . $personalInfo['lastName'],
                'email' => $personalInfo['email'],
                'password' => Hash::make($defaultPassword),
                'role' => 'student',
            ]);

            $student = Student::create([
                'student_id' => $personalInfo['studentId'],
                'user_id' => $user->id,
                'first_name' => $personalInfo['firstName'],
                'middle_name' => $personalInfo['middleName'] ?? null,
                'last_name' => $personalInfo['lastName'],
                'email' => $personalInfo['email'],
                'phone' => $personalInfo['phone'],
                'date_of_birth' => $personalInfo['dateOfBirth'],
                'age' => $personalInfo['age'],
                'gender' => $personalInfo['gender'],
                'address' => $personalInfo['address'],
                'city' => $personalInfo['city'],
                'province' => $personalInfo['province'],
                'postal_code' => $personalInfo['postalCode'],
                'emergency_contact_name' => $personalInfo['emergencyContact']['name'],
                'emergency_contact_relationship' => $personalInfo['emergencyContact']['relationship'],
                'emergency_contact_phone' => $personalInfo['emergencyContact']['phone'],
                'current_year' => $academicStanding['currentYear'],
                'current_semester' => $academicStanding['currentSemester'],
                'current_gpa' => $academicStanding['currentGPA'],
                'total_units' => $academicStanding['totalUnits'] ?? 0,
                'standing' => $academicStanding['standing'],
                'advisor' => $academicStanding['advisor'] ?? null,
                'is_active' => true,
            ]);
        } else {
            // Handle old flat format (for backward compatibility)
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
        }

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
        // Support both old flat structure and new nested structure from frontend
        $isNestedFormat = $request->has('personalInfo') || $request->has('academicStanding');

        if ($isNestedFormat) {
            // Handle nested format from StudentProfilingView.vue
            $request->validate([
                'personalInfo.firstName' => 'required|string|max:100',
                'personalInfo.lastName' => 'required|string|max:100',
                'personalInfo.middleName' => 'nullable|string|max:100',
                'personalInfo.studentId' => 'required|string|max:50',
                'personalInfo.email' => 'required|email|unique:students,email,' . $student->id,
                'personalInfo.phone' => 'required|string|max:50',
                'personalInfo.dateOfBirth' => 'required|date',
                'personalInfo.age' => 'required|integer|min:16|max:100',
                'personalInfo.gender' => 'required|in:male,female,other',
                'personalInfo.address' => 'required|string|max:255',
                'personalInfo.city' => 'required|string|max:100',
                'personalInfo.province' => 'required|string|max:100',
                'personalInfo.postalCode' => 'required|string|max:20',
                'personalInfo.emergencyContact.name' => 'required|string|max:100',
                'personalInfo.emergencyContact.relationship' => 'required|string|max:50',
                'personalInfo.emergencyContact.phone' => 'required|string|max:50',
                'academicStanding.currentYear' => 'required|integer|min:1|max:4',
                'academicStanding.currentSemester' => 'required|in:first,second,summer',
                'academicStanding.currentGPA' => 'required|numeric|min:0|max:4',
                'academicStanding.totalUnits' => 'nullable|integer|min:0',
                'academicStanding.standing' => 'required|in:good,average,warning,probation',
                'academicStanding.advisor' => 'nullable|string|max:100',
            ]);

            $personalInfo = $request->input('personalInfo');
            $academicStanding = $request->input('academicStanding');

            $student->update([
                'student_id' => $personalInfo['studentId'],
                'first_name' => $personalInfo['firstName'],
                'middle_name' => $personalInfo['middleName'] ?? null,
                'last_name' => $personalInfo['lastName'],
                'email' => $personalInfo['email'],
                'phone' => $personalInfo['phone'],
                'date_of_birth' => $personalInfo['dateOfBirth'],
                'age' => $personalInfo['age'],
                'gender' => $personalInfo['gender'],
                'address' => $personalInfo['address'],
                'city' => $personalInfo['city'],
                'province' => $personalInfo['province'],
                'postal_code' => $personalInfo['postalCode'],
                'emergency_contact_name' => $personalInfo['emergencyContact']['name'],
                'emergency_contact_relationship' => $personalInfo['emergencyContact']['relationship'],
                'emergency_contact_phone' => $personalInfo['emergencyContact']['phone'],
                'current_year' => $academicStanding['currentYear'],
                'current_semester' => $academicStanding['currentSemester'],
                'current_gpa' => $academicStanding['currentGPA'],
                'total_units' => $academicStanding['totalUnits'] ?? 0,
                'standing' => $academicStanding['standing'],
                'advisor' => $academicStanding['advisor'] ?? null,
            ]);

            if ($student->user) {
                $student->user->update([
                    'name' => $personalInfo['firstName'] . ' ' . $personalInfo['lastName'],
                    'email' => $personalInfo['email'],
                ]);
            }
        } else {
            // Handle old flat format (for backward compatibility)
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
