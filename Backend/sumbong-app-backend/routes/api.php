<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceTypeController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\FeedbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);

    // Service Types
    Route::get('/service-types', [ServiceTypeController::class, 'index']);
    Route::get('/service-types/{id}', [ServiceTypeController::class, 'show']);

    // Requests
    Route::apiResource('requests', RequestController::class);
    Route::post('/requests/{id}/assign', [RequestController::class, 'assign']);
    Route::put('/requests/{id}/status', [RequestController::class, 'updateStatus']);

    // Attachments
    Route::post('/requests/{requestId}/attachments', [AttachmentController::class, 'store']);
    Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Feedback
    Route::post('/requests/{requestId}/feedback', [FeedbackController::class, 'store']);
    Route::get('/requests/{requestId}/feedback', [FeedbackController::class, 'index']);

    // Admin routes
    Route::middleware(['admin'])->group(function () {
        // Service Types Management
        Route::post('/service-types', [ServiceTypeController::class, 'store']);
        Route::put('/service-types/{id}', [ServiceTypeController::class, 'update']);
        Route::delete('/service-types/{id}', [ServiceTypeController::class, 'destroy']);

        // Users Management
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::get('/admin/users/{id}', [UserController::class, 'show']);
        Route::put('/admin/users/{id}', [UserController::class, 'update']);
        Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);

        // Requests Management
        Route::get('/admin/requests', [RequestController::class, 'adminIndex']);
        Route::get('/admin/requests/{id}', [RequestController::class, 'adminShow']);

        // Notifications Management
        Route::post('/admin/notifications', [NotificationController::class, 'store']);
        Route::put('/admin/notifications/{id}', [NotificationController::class, 'update']);
        Route::delete('/admin/notifications/{id}', [NotificationController::class, 'destroy']);

        // Dashboard Stats
        Route::get('/admin/stats', [RequestController::class, 'stats']);
    });
});

