<?php

use App\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;

// Announcements API routes
Route::prefix('announcements')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
    Route::get('/recent', [AnnouncementController::class, 'recent']);
    Route::post('/', [AnnouncementController::class, 'store'])->middleware('admin');
    Route::get('/{id}', [AnnouncementController::class, 'show']);
    Route::put('/{id}', [AnnouncementController::class, 'update'])->middleware('admin');
    Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->middleware('admin');
    
    // Comments
    Route::get('/{id}/comments', [AnnouncementController::class, 'getComments']);
    Route::post('/{id}/comments', [AnnouncementController::class, 'addComment']);
    Route::delete('/{announcementId}/comments/{commentId}', [AnnouncementController::class, 'deleteComment']);
    
    // Likes
    Route::post('/{id}/like', [AnnouncementController::class, 'toggleLike']);
    
    // Attachments
    Route::get('/attachments/{id}/download', [AnnouncementController::class, 'downloadAttachment']);
});
