<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    public function index()
    {
        try {
            $students = Student::with([
                'academicHistory',
                'activities',
                'skills',
                'affiliations',
                'violations',
                'courseEnrollments.course',
            ])->orderByDesc('id')->get();

            $mapped = $students->map(function (Student $student) {
                return $this->transformStudentForFrontend($student);
            })->values();

            return response()->json([
                'students' => $mapped,
                'total' => $mapped->count(),
                'page' => 1,
                'limit' => max($mapped->count(), 1),
                'totalPages' => 1,
            ]);
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

            return response()->json(
                $this->transformStudentForFrontend(
                    $student->load([
                        'academicHistory',
                        'activities',
                        'skills',
                        'affiliations',
                        'violations',
                        'courseEnrollments.course',
                    ])
                ),
                201
            );
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

        return response()->json(
            $this->transformStudentForFrontend(
                $student->load([
                    'academicHistory',
                    'activities',
                    'skills',
                    'affiliations',
                    'violations',
                    'courseEnrollments.course',
                ])
            ),
            201
        );
    }

    public function show(Student $student)
    {
        $student->load([
            'academicHistory',
            'activities',
            'skills',
            'affiliations',
            'violations',
            'courseEnrollments.course',
        ]);

        return response()->json($this->transformStudentForFrontend($student));
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

        return response()->json(
            $this->transformStudentForFrontend(
                $student->load([
                    'academicHistory',
                    'activities',
                    'skills',
                    'affiliations',
                    'violations',
                    'courseEnrollments.course',
                ])
            )
        );
    }

    public function destroy(Student $student)
    {
        try {
            // Soft delete (archive) the student
            $student->delete();
            
            // Also soft delete the associated user if exists
            if ($student->user) {
                $student->user->delete();
            }

            return response()->json([
                'message' => 'Student archived successfully',
                'student_id' => $student->id,
                'archived_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error archiving student: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to archive student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $student = Student::withTrashed()->findOrFail($id);
            
            if (!$student->trashed()) {
                return response()->json([
                    'message' => 'Student is not archived'
                ], 400);
            }

            // Restore the student
            $student->restore();
            
            // Also restore the associated user if exists and is trashed
            if ($student->user && $student->user->trashed()) {
                $student->user->restore();
            }

            return response()->json([
                'message' => 'Student restored successfully',
                'student_id' => $student->id,
                'restored_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error restoring student: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to restore student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function archived()
    {
        try {
            $archivedStudents = Student::onlyTrashed()
                ->with('user')
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'student_number' => $student->student_id,
                        'email' => $student->email,
                        'year_level' => $student->year_level ?? $student->current_year,
                        'academic_standing' => $student->standing,
                        'gpa' => $student->current_gpa,
                        'deleted_at' => $student->deleted_at,
                        'user' => $student->user ? [
                            'email' => $student->user->email,
                            'deleted_at' => $student->user->deleted_at
                        ] : null
                    ];
                });

            return response()->json($archivedStudents);
        } catch (\Exception $e) {
            \Log::error('Error fetching archived students: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch archived students',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function generateSampleData(): JsonResponse
    {
        $samples = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@example.com',
                'age' => 20,
                'blood_type' => 'O+',
                'disability_status' => false,
                'disability_name' => null,
                'scholar' => true,
                'working_student' => false,
                'contact_number' => '09123456789',
                'address' => '123 Main St, Manila',
                'section' => 'BSIT-3A',
                'year_level' => '3rd',
                'program' => 'BSIT',
                'gender' => 'Male',
                'birth_date' => '2003-05-15',
                'is_at_risk' => false,
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@example.com',
                'age' => 19,
                'blood_type' => 'A+',
                'disability_status' => false,
                'disability_name' => null,
                'scholar' => false,
                'working_student' => true,
                'contact_number' => '09234567890',
                'address' => '456 Oak Ave, Quezon City',
                'section' => 'BSCS-2B',
                'year_level' => '2nd',
                'program' => 'BSCS',
                'gender' => 'Female',
                'birth_date' => '2004-08-22',
                'is_at_risk' => false,
            ],
            [
                'first_name' => 'Jose',
                'last_name' => 'Reyes',
                'email' => 'jose.reyes@example.com',
                'age' => 21,
                'blood_type' => 'B+',
                'disability_status' => false,
                'disability_name' => null,
                'scholar' => true,
                'working_student' => true,
                'contact_number' => '09345678901',
                'address' => '789 Pine Rd, Makati',
                'section' => 'BSIT-4A',
                'year_level' => '4th',
                'program' => 'BSIT',
                'gender' => 'Male',
                'birth_date' => '2002-12-10',
                'is_at_risk' => true,
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'email' => 'ana.garcia@example.com',
                'age' => 18,
                'blood_type' => 'AB+',
                'disability_status' => false,
                'disability_name' => null,
                'scholar' => false,
                'working_student' => false,
                'contact_number' => '09456789012',
                'address' => '321 Elm St, Pasay',
                'section' => 'BSCS-1A',
                'year_level' => '1st',
                'program' => 'BSCS',
                'gender' => 'Female',
                'birth_date' => '2005-03-08',
                'is_at_risk' => false,
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Lopez',
                'email' => 'carlos.lopez@example.com',
                'age' => 22,
                'blood_type' => 'O-',
                'disability_status' => true,
                'disability_name' => 'Visual Impairment',
                'scholar' => true,
                'working_student' => false,
                'contact_number' => '09567890123',
                'address' => '654 Maple Dr, Mandaluyong',
                'section' => 'BSIT-3B',
                'year_level' => '3rd',
                'program' => 'BSIT',
                'gender' => 'Male',
                'birth_date' => '2001-07-19',
                'is_at_risk' => false,
            ],
        ];

        $created = [];
        foreach ($samples as $data) {
            $existing = Student::where('email', $data['email'])->first();
            if (!$existing) {
                $created[] = Student::create($data);
            }
        }

        return response()->json([
            'message' => count($created) . ' sample students generated',
            'students' => $created,
        ]);
    }

    private function transformStudentForFrontend(Student $student): array
    {
        return [
            'id' => $student->id,
            'personalInfo' => $student->personal_info,
            'academicHistory' => $student->academicHistory->map(function ($history) {
                return [
                    'id' => $history->id,
                    'schoolName' => $history->school_name,
                    'degree' => $history->degree,
                    'major' => $history->major,
                    'startDate' => optional($history->start_date)->format('Y-m-d'),
                    'endDate' => optional($history->end_date)->format('Y-m-d'),
                    'gpa' => $history->gpa !== null ? (float) $history->gpa : null,
                    'honors' => $history->honors ?? [],
                    'status' => $history->status,
                ];
            })->values(),
            'academicStanding' => $student->academic_standing,
            'activities' => $student->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'type' => $activity->type,
                    'role' => $activity->role,
                    'startDate' => optional($activity->start_date)->format('Y-m-d'),
                    'endDate' => optional($activity->end_date)->format('Y-m-d'),
                    'description' => $activity->description,
                    'achievements' => $activity->achievements ?? [],
                    'level' => $activity->level,
                ];
            })->values(),
            'violations' => $student->violations->map(function ($violation) {
                return [
                    'id' => $violation->id,
                    'type' => $violation->type,
                    'severity' => $violation->severity,
                    'description' => $violation->description,
                    'date' => optional($violation->date)->format('Y-m-d'),
                    'status' => $violation->status,
                    'sanctions' => $violation->consequence ? [$violation->consequence] : [],
                    'points' => 0,
                    'reportedBy' => 'System',
                ];
            })->values(),
            'skills' => $student->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'category' => $skill->category,
                    'proficiency' => $skill->proficiency,
                    'certifications' => $skill->certifications ? [$skill->certifications] : [],
                    'yearsExperience' => $skill->years_experience,
                    'lastUsed' => optional($skill->last_used)->format('Y-m-d'),
                ];
            })->values(),
            'affiliations' => $student->affiliations->map(function ($affiliation) {
                return [
                    'id' => $affiliation->id,
                    'name' => $affiliation->name,
                    'type' => $affiliation->type,
                    'role' => $affiliation->role,
                    'startDate' => optional($affiliation->start_date)->format('Y-m-d'),
                    'endDate' => optional($affiliation->end_date)->format('Y-m-d'),
                    'position' => $affiliation->position,
                    'description' => $affiliation->description,
                ];
            })->values(),
            'enrolledCourses' => $student->courseEnrollments->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'courseId' => $enrollment->course_id,
                    'courseCode' => optional($enrollment->course)->code,
                    'courseName' => optional($enrollment->course)->name,
                    'semester' => $enrollment->semester,
                    'academicYear' => $enrollment->academic_year,
                    'status' => $enrollment->status,
                    'grade' => $enrollment->grade !== null ? (float) $enrollment->grade : null,
                ];
            })->values(),
            'createdAt' => optional($student->created_at)->toISOString(),
            'updatedAt' => optional($student->updated_at)->toISOString(),
            'isActive' => (bool) $student->is_active,
        ];
    }
}
