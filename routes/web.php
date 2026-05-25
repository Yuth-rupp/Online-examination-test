<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* --- 🌐 Portal Welcome / Landing Page --- */
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
    
    // 1. View Login Page
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.page');

    // 2. View Registration Page
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register.page');
    
    // 3. Display the exact design registration success confirmation screen
    Route::get('/register/success', function () {
        // If the session flash memory has no verification data, bounce back to register page safely
        if (!session()->has('registered_email')) {
            return redirect()->route('register.page');
        }
        // ✅ UPDATED: Points to resources/views/auth/register_success.blade.php cleanly
        return view('auth.register_success');
    })->name('register.success');

    // 4. Forgot Password Page Layout View
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // 5. Handle Forgot Password Form Submission Link Generation
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // 🌟 5b. Display the precise "Check your email" view structure success state
    Route::get('/forgot-password/success', function () {
        // Keep the route protected from casual path URL jumping if a token wasn't just fired
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.status-email');
    })->name('password.success');

    // 6. View the actual password reset form page when users click the link from their email log
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    // 7. Handle updating the database table with the new password credentials
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Secure Form Processing Actions Group
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });
});

/* --- 🛡️ Protected Dashboard Core Group (Requires Active Browser Session Login) --- */
Route::middleware(['auth'])->group(function () {
    
    // 1. Teacher Metric Analytics Workspace Dashboard View Layout
    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->name('teacher.dashboard');

    // 2. Personalization Profile Settings View Layout
    Route::get('/teacher/settings', function () {
        return view('teacher.settings');
    })->name('teacher.settings');
    
    // 3. Nested Teacher Management Control Tree Module
    Route::prefix('teacher')->group(function () {
        Route::get('/courses', [TeacherController::class, 'myCourses'])->name('teacher.courses');
        Route::post('/exams', [TeacherController::class, 'createExam'])->name('exams.store');
        
        Route::get('/questions/create', function() {
            return view('teacher.create_question'); 
        })->name('questions.create');

        Route::post('/questions', [TeacherController::class, 'addQuestion'])->name('questions.store');
        Route::get('/exams/{examId}/submissions', [TeacherController::class, 'submissions'])->name('exams.submissions');
    });

    // 🚪 Stateful Security Session Logout Endpoint Action
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
});