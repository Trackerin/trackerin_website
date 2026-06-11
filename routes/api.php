<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TodoController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\CurriculumController;
use App\Http\Controllers\Api\V1\MilestoneController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\NotificationController;

Route::prefix('v1')->group(function () {
    // Public Auth Routes
    Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/google', [AuthController::class, 'googleTokenLogin']);
    Route::post('/forgot-password/send-otp', [AuthController::class, 'sendForgotPasswordOtp']);
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);
    
    // Public Contact Route
    Route::post('/contact', [ContactController::class, 'store']);

    // Protected Auth Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Core CRUD APIs
        Route::apiResource('todos', TodoController::class);
        Route::apiResource('notes', NoteController::class);
        
        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        
        // Curriculum & Milestones (Read & Update Progress)
        Route::post('curriculums/generate', [CurriculumController::class, 'generate'])->middleware('throttle:ai-generator');
        Route::get('curriculums', [CurriculumController::class, 'index']);
        Route::get('curriculums/{curriculum}', [CurriculumController::class, 'show']);
        Route::delete('curriculums/{curriculum}', [CurriculumController::class, 'destroy']);
        Route::put('milestones/{milestone}/complete', [MilestoneController::class, 'updateProgress']);
        Route::post('milestones/{milestone}/generate-quiz', [MilestoneController::class, 'generateQuiz'])->middleware('throttle:ai-generator');
    });
});
