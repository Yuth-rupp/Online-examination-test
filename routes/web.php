<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* --- 🌐 Portal Welcome / Landing Page --- */
Route::get('/', function () {
    // If the user is already logged in, route them straight to their respective workspace!
    if (Auth::check()) {
        $role = Auth::user()->role;
        
        switch ($role) {
            case 'super_admin':
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
            default:
                return redirect()->route('student.dashboard');
        }
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
    
    // 3. Display registration success confirmation screen
    Route::get('/register/success', function () {
        // If the session flash memory has no verification data, bounce back to register safely
        if (!session()->has('registered_email')) {
            return redirect()->route('register.page');
        }
        return view('auth.register_success');
    })->name('register.success');

    // 4. Forgot Password Page Layout View
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // 5. Handle Forgot Password Form Submission Link Generation
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // 5b. Display the precise "Check your email" view structure success state
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

    // --- 👑 SUPER ADMIN PASSWORDLESS LOGIN FLOW ---
    
    // 1. Show the "Enter Email" page
    Route::get('/superadmin/login', function () {
        return view('auth.superadmin-login');
    })->name('superadmin.login.page');

    // 2. Process the email & send the OTP code
    Route::post('/superadmin/login', [AuthController::class, 'sendSuperAdminCode'])->name('superadmin.sendcode');

    // 3. Show the "Enter 6-Digit Code" page
    Route::get('/superadmin/verify', function () {
        return view('auth.superadmin-verify');
    })->name('superadmin.verify.page');
    
    // 4. Process the 6-Digit Code & Log them in
    Route::post('/superadmin/verify', [AuthController::class, 'verifySuperAdminCode'])->name('superadmin.verify');

    // 5. Super Admin Forgot Password Page Layout View
    Route::get('/superadmin/forgot-password', function () {
        return view('auth.superadmin-forgot-password');
    })->name('superadmin.password.request');
});

/* --- 🛡️ Protected Routes (Requires Active Browser Session Login) --- */

// 🚪 Stateful Security Session Logout Endpoint Action (Available to ALL logged-in roles)
Route::middleware(['auth'])->post('/auth/logout', [AuthController::class, 'logout'])->name('logout');


/* --- 👨‍🏫 TEACHER ONLY ROUTES --- */
Route::middleware(['auth', 'role:teacher'])->group(function () {
    
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

        // Note: These paths reflect standard RESTful architecture setup
        Route::post('/questions', [TeacherController::class, 'addQuestion'])->name('questions.store');
        Route::get('/exams/{examId}/submissions', [TeacherController::class, 'submissions'])->name('exams.submissions');
    });
});

/* --- 🎓 STUDENT ONLY ROUTES --- */
Route::middleware(['auth', 'role:student'])->group(function () {
    
    // 1. Primary Student Dashboard Landing Page View via Controller Method
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    
    // 2. Dynamic Settings View and Profile Form Action Hooks
    Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    Route::post('/student/settings/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    
    // 3. Sidebar Navigation Secondary Fallback Placeholders
    Route::get('/student/exams', function () {
        return view('student.exams'); 
    })->name('student.exams');

    Route::get('/student/history', function () {
        return view('student.history'); 
    })->name('student.history');

    Route::get('/student/support', function () {
        return view('student.support'); 
    })->name('student.support');
});

/* --- 👑 ADMIN & SUPER ADMIN ONLY ROUTES --- */
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    
    // 1. Main Admin Dashboard View Link (Calculates dynamic layout metric states)
    Route::get('/admin/dashboard', function () {
        // Fetch dynamic registered user count values
        $totalUsers = \App\Models\User::count(); 

        // Compute active matching exam sessions running right now
        $activeExams = \App\Models\Exam::where('status', 'published')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();

        // Provide a safe, realistic system load visualization percentage
        $cpuUsage = rand(12, 45); 

        // Populate system integrity flag indicators for your monitor pane component
        $proctorFlags = [
            [
                'student' => 'Sem Vatenakpanha',
                'exam' => 'Midterm Mathematics Exam',
                'violations' => 3,
                'time' => now()->subMinutes(4)->format('h:i A')
            ],
            [
                'student' => 'Yun Dalin',
                'exam' => 'Database Systems Quiz',
                'violations' => 1,
                'time' => now()->subMinutes(12)->format('h:i A')
            ]
        ];

        // Safely extract system actions audit trail entries from the model tier
        if (class_exists('\App\Models\AuditLog')) {
            $systemLogs = \App\Models\AuditLog::orderBy('created_at', 'desc')->take(5)->get();
        } else {
            $systemLogs = collect([]);
        }

        return view('admin.dashboard', compact('totalUsers', 'activeExams', 'cpuUsage', 'proctorFlags', 'systemLogs')); 
    })->name('admin.dashboard');

    // 2. System Backups Administrative Control Route
    Route::get('/admin/backup', function () {
        return view('admin.backup');
    })->name('admin.backup');

    // 3. Core System Parameters Configuration Form Route
    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
});