<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TeacherController;

/* --- Portal Welcome / Landing Page --- */
/* --- Portal Welcome / Landing Page --- */
Route::get('/', function () {
    // If the teacher is already logged in, send them straight to the workspace dashboard!
    if (Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('teacher.dashboard');
    }
    
    // Otherwise, send guests to the login page cleanly
    return redirect()->route('login.page');
});
/* --- 🚪 Guest-Only Group (Prevents Back-Button Session Degradation) --- */
Route::middleware(['guest'])->group(function () {
    
    // View Login Page
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.page');

    // View Registration Page
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register.page');

    // Secure Form Processing Endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });
});

/* --- 🛡️ Protected Dashboard Core Group (Requires Active Browser Session) --- */
Route::middleware(['auth'])->group(function () {
    
    // 1. Teacher Metric Analytics Workspace Dashboard View
    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->name('teacher.dashboard');

    // 2. Personalization Profile Settings View Layout
    Route::get('/teacher/settings', function () {
        return view('teacher.settings');
    })->name('teacher.settings');
    
    // 3. Nested Teacher Module Route Tree
    Route::prefix('teacher')->group(function () {
        Route::get('/courses', [TeacherController::class, 'myCourses'])->name('teacher.courses');
        Route::post('/exams', [TeacherController::class, 'createExam'])->name('exams.store');
        
        Route::get('/questions/create', function() {
            return view('teacher.create_question'); 
        })->name('questions.create');

        Route::post('/questions', [TeacherController::class, 'addQuestion'])->name('questions.store');
        Route::get('/exams/{examId}/submissions', [TeacherController::class, 'submissions'])->name('exams.submissions');
    });

    // 🚪 Stateful Logout Pipeline Action 
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
});