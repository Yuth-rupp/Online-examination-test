<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/* --- Public Mobile App API Routes --- */
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/* --- Protected Mobile Routes (Requires Valid Bearer Token) --- */
Route::middleware('auth:sanctum')->group(function () {
    
    // Fetch logged-in mobile user account details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 🧑‍🏫 Teacher API Endpoints for Mobile Layouts
    Route::prefix('teacher')->group(function () {
        Route::get('/courses', [TeacherController::class, 'myCourses']);
        Route::post('/exams', [TeacherController::class, 'createExam']);
        Route::post('/questions', [TeacherController::class, 'addQuestion']);
        Route::get('/exams/{examId}/submissions', [TeacherController::class, 'submissions']);
    });

    // 🚪 Mobile Logout Session
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});