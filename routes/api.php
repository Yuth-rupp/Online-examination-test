<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Import your controllers from the Api subfolder
*/

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;

// 1. PUBLIC ROUTES (No Token Needed)
// These handle registration and login to generate your Sanctum token
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


// 2. PROTECTED ROUTES (Requires 'Authorization: Bearer <token>')
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Exam Dashboard
    Route::get('/exams', [ExamController::class, 'index']); 
    
    // Exam Taking
    Route::post('/exams/{id}/submit', [ExamController::class, 'submit']);

    // Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});