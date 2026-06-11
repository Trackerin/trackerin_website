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
    Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp'])->middleware('throttle:3,1');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/login/google', [AuthController::class, 'googleTokenLogin'])->middleware('throttle:10,1');
    Route::post('/forgot-password/send-otp', [AuthController::class, 'sendForgotPasswordOtp'])->middleware('throttle:3,1');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    
    // Public Contact Route
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:3,1');

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
        Route::post('milestones/{milestone}/quiz/submit', [MilestoneController::class, 'submitQuiz']);
    });
});
