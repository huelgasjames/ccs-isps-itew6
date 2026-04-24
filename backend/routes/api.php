<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TalentController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentActivityController;
use App\Http\Controllers\StudentAcademicHistoryController;
use App\Http\Controllers\StudentAffiliationController;
use App\Http\Controllers\SyllabusController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API working'
    ]);
});

Route::get('/test-students', function () {
    try {
        $students = \App\Models\Student::get(['id', 'first_name', 'last_name']);
        return response()->json([
            'count' => $students->count(),
            'students' => $students
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test-users', function () {
    try {
        $users = \App\Models\User::get(['id', 'email', 'role', 'name']);
        return response()->json([
            'count' => $users->count(),
            'users' => $users
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['demo.auth', 'auth:sanctum']);
Route::get('/me', [AuthController::class, 'me'])->middleware(['demo.auth', 'auth:sanctum']);

// Debug route
Route::post('/debug-login', function(Request $request) {
    return response()->json([
        'received_data' => $request->all(),
        'headers' => $request->headers->all(),
        'content_type' => $request->header('Content-Type'),
        'method' => $request->method()
    ]);
});

// Announcement routes (temporarily public for testing)
Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::get('/announcements/recent', [AnnouncementController::class, 'recent']);
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);

// Student routes (temporarily public for testing)
Route::apiResource('students', StudentController::class);
Route::post('/students/generate-sample-data', [StudentController::class, 'generateSampleData']);

// Archive management routes
Route::get('/students/archived', [StudentController::class, 'archived']);
Route::post('/students/{id}/restore', [StudentController::class, 'restore']);

// Professor routes (temporarily public for testing)
Route::apiResource('professors', ProfessorController::class);
Route::post('/professors/generate-sample-data', [ProfessorController::class, 'generateSampleData']);

// Demo routes - handle demo tokens without sanctum
Route::middleware('demo.auth')->group(function () {
    // Courses routes
    Route::apiResource('courses', CourseController::class);
    Route::get('/courses/analytics', [CourseController::class, 'analytics']);
    Route::post('/courses/generate-sample-data', [CourseController::class, 'generateSampleData']);
    Route::patch('/courses/{course}/archive', [CourseController::class, 'archive']);
    Route::get('/courses/{course}/enrollments', [CourseController::class, 'enrollments']);
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollStudent']);
    Route::delete('/enrollments/{enrollment}', [CourseController::class, 'dropCourse']);
    Route::patch('/enrollments/{enrollment}/grade', [CourseController::class, 'assignGrade']);
    Route::patch('/enrollments/{enrollment}', [CourseController::class, 'updateEnrollment']);
    Route::post('/courses/{course}/bulk-enroll', [CourseController::class, 'bulkEnroll']);
    Route::post('/courses/{course}/bulk-drop', [CourseController::class, 'bulkDrop']);
    Route::get('/courses/{course}/enrollment-stats', [CourseController::class, 'enrollmentStats']);
    
    // Syllabus routes
    Route::apiResource('syllabi', SyllabusController::class);
    Route::post('/syllabi/generate-sample-data', [SyllabusController::class, 'generateSampleData']);
    Route::patch('/syllabi/{syllabus}/approve', [SyllabusController::class, 'approve']);
    Route::patch('/syllabi/{syllabus}/reject', [SyllabusController::class, 'reject']);
    Route::post('/syllabi/{syllabus}/upload-file', [SyllabusController::class, 'uploadFile']);
    Route::get('/syllabi/{syllabus}/download-file', [SyllabusController::class, 'downloadFile']);
    Route::get('/courses/{course}/syllabi', [SyllabusController::class, 'getByCourse']);
    Route::get('/professors/{professor}/syllabi', [SyllabusController::class, 'getByProfessor']);
    
    // Events routes
    Route::apiResource('events', EventController::class);
    Route::post('/events/generate-sample', [EventController::class, 'generateSampleData']);
});

// Protected routes - require real authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('violations', ViolationController::class);
    Route::apiResource('skills', SkillController::class);
    Route::apiResource('talents', TalentController::class);
    Route::apiResource('sports', SportController::class);
    Route::apiResource('certificates', CertificateController::class);
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('student-activities', StudentActivityController::class);
    Route::apiResource('student-academic-history', StudentAcademicHistoryController::class);
    Route::apiResource('student-affiliations', StudentAffiliationController::class);
    
    Route::get('/students/at-risk', [StudentController::class, 'getAtRiskStudents']);
    Route::post('/violations/{violation}/resolve', [ViolationController::class, 'resolveViolation']);
    Route::get('/students/{student}/violations', [ViolationController::class, 'getStudentViolations']);
    Route::get('/violations/pending', [ViolationController::class, 'getPendingViolations']);
    
    // Student-specific routes
    Route::get('/students/{student}/activities', [StudentActivityController::class, 'getStudentActivities']);
    Route::get('/students/{student}/history', [StudentAcademicHistoryController::class, 'getStudentHistory']);
    Route::get('/students/{student}/affiliations', [StudentAffiliationController::class, 'getStudentAffiliations']);
    
    // Event specific routes
    Route::post('/events/{event}/register', [EventController::class, 'register']);
    Route::post('/events/{event}/unregister', [EventController::class, 'unregister']);
    Route::get('/events/my-registrations', [EventController::class, 'myRegistrations']);
    
    // Protected announcement routes
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
    
    // Announcement comments
    Route::get('/announcements/{id}/comments', [AnnouncementController::class, 'getComments']);
    Route::post('/announcements/{id}/comments', [AnnouncementController::class, 'addComment']);
    Route::delete('/announcements/{announcementId}/comments/{commentId}', [AnnouncementController::class, 'deleteComment']);
    
    // Announcement likes
    Route::post('/announcements/{id}/like', [AnnouncementController::class, 'toggleLike']);
    
    // Announcement views
    Route::post('/announcements/{id}/view', [AnnouncementController::class, 'markAsViewed']);
    
    // Announcement attachments
    Route::get('/announcements/attachments/{id}/download', [AnnouncementController::class, 'downloadAttachment']);
});