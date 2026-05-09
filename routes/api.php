<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TodoController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\CurriculumController;
use App\Http\Controllers\Api\V1\MilestoneController;

Route::prefix('v1')->group(function () {
    // Public Auth Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/google', [AuthController::class, 'googleTokenLogin']);

    // Protected Auth Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Core CRUD APIs
        Route::apiResource('todos', TodoController::class);
        Route::apiResource('notes', NoteController::class);
        
        // Curriculum & Milestones (Read & Update Progress)
        Route::get('curriculums', [CurriculumController::class, 'index']);
        Route::get('curriculums/{curriculum}', [CurriculumController::class, 'show']);
        Route::put('milestones/{milestone}/complete', [MilestoneController::class, 'updateProgress']);
    });
});
