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

Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API working'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('students', StudentController::class);
    Route::apiResource('professors', ProfessorController::class);
    Route::apiResource('violations', ViolationController::class);
    Route::apiResource('skills', SkillController::class);
    Route::apiResource('talents', TalentController::class);
    Route::apiResource('sports', SportController::class);
    Route::apiResource('certificates', CertificateController::class);
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('events', EventController::class);
    
    Route::get('/students/at-risk', [StudentController::class, 'getAtRiskStudents']);
    Route::post('/violations/{violation}/resolve', [ViolationController::class, 'resolveViolation']);
    Route::get('/students/{student}/violations', [ViolationController::class, 'getStudentViolations']);
    Route::get('/violations/pending', [ViolationController::class, 'getPendingViolations']);
    
    // Event specific routes
    Route::post('/events/{event}/register', [EventController::class, 'register']);
    Route::post('/events/{event}/unregister', [EventController::class, 'unregister']);
    Route::get('/events/my-registrations', [EventController::class, 'myRegistrations']);
});