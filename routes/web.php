<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\GradingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* --- 🌐 Portal Welcome / Dynamic Landing Redirector --- */
Route::get('/', function () {
    // If the user context possesses an active session state, forward them to their workspace
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
    
    // Otherwise, send anonymous guest tracking instances directly to the entry login panel
    return redirect()->route('login.page');
});

/* --- 🚪 Guest-Only Group (Prevents Back-Button Session Degradation) --- */
Route::middleware(['guest'])->group(function () {
    
    // 1. View Login Page Template
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.page');

    // 2. View Registration Page Layout
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register.page');
    
    // 3. Display registration success confirmation card screen
    Route::get('/register/success', function () {
        if (!session()->has('registered_email')) {
            return redirect()->route('register.page');
        }
        return view('auth.register_success');
    })->name('register.success');

    // 4. Forgot Password Email Initialization View
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // 5. Handle Recovery Token Dispatch Routine Actions
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // 5b. Display isolated email transmission verification success checkpoints
    Route::get('/forgot-password/success', function () {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.status-email');
    })->name('password.success');

    // 6. View secure link form incoming validation string token target
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    // 7. Commit new password update transaction parameters inside users schemas
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Secure Pipeline Form Authentication Handlers Namespace Prefix
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    // --- 👑 SUPER ADMIN PASSWORDLESS LOGIN FLOW ---
    
    // 1. Show the "Enter Email" root credential landing check
    Route::get('/superadmin/login', function () {
        return view('auth.superadmin-login');
    })->name('superadmin.login.page');

    // 2. Evaluate target address database privileges & cache numeric verification token
    Route::post('/superadmin/login', [AuthController::class, 'sendSuperAdminCode'])->name('superadmin.sendcode');

    // 3. Render 6-digit cryptographic text matrix collection box interface
    Route::get('/superadmin/verify', function () {
        return view('auth.superadmin-verify');
    })->name('superadmin.verify.page');
    
    // 4. Validate match array states and sign user into session application tracking space
    Route::post('/superadmin/verify', [AuthController::class, 'verifySuperAdminCode'])->name('superadmin.verify');

    // 5. Emergency disaster recovery panel interface point route mapping
    Route::get('/superadmin/forgot-password', function () {
        return view('auth.superadmin-forgot-password');
    })->name('superadmin.password.request');
});

/* --- 🛡️ Protected Routes (Requires Active Browser Session Login) --- */

// 🚪 Stateful Session Destruction Security Termination Hook (Available across all authenticated roles)
Route::middleware(['auth'])->post('/auth/logout', [AuthController::class, 'logout'])->name('logout');


/* --- 👨‍🏫 TEACHER WORKSPACE ONLY SEGMENT ROUTING TREE --- */
Route::middleware(['auth', 'role:teacher'])->group(function () {
    
    // 1. Primary Dashboard Navigation Handler Core Fix (Loads index dataset metrics)
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');

    // 2. Metrics Analytic Evaluation Trend Chart Data Pipeline Method Interface
    Route::get('/teacher/analytics', [TeacherController::class, 'analytics'])->name('teacher.analytics');

    // 3. User Identity Profile Personalization Configurations View Layout 
    Route::get('/teacher/settings', function () {
        return view('teacher.settings');
    })->name('teacher.settings');
    
    // Process form profile upload parameters transformations updates
    Route::post('/teacher/settings', [TeacherController::class, 'updateSettings'])->name('teacher.settings.update');

    /* --- 📂 WEB-BASED INTERACTIVE COURSE MANAGEMENT FOR TEACHERS --- */
    // Resolved 403 authorization boundary issues by organizing endpoints safely inside structural blocks
    Route::get('/teacher/courses/create', [TeacherController::class, 'createCourse'])->name('teacher.courses.create');
    Route::post('/teacher/courses/store', [TeacherController::class, 'storeCourse'])->name('teacher.courses.store');
    Route::delete('/teacher/courses/{id}', [TeacherController::class, 'destroyCourse'])->name('teacher.courses.destroy');

    /* --- 📑 WEB-BASED COMPREHENSIVE EXAMINATION QUESTION OVERVIEW PREVIEW --- */
    // Synchronized cleanly to track parameters mapping direct across lists
    Route::get('/teacher/exams/{id}/preview', [TeacherController::class, 'previewExam'])->name('teacher.exams.preview');

    /* --- 🖥️ PROCTORROOM: LIVE EXAM MONITORING INTEGRATIONS --- */
    
    // Main Telemetry Monitor Panel Interface Dashboard Template
    Route::get('/teacher/monitoring', function () {
        return view('teacher.monitoring');
    })->name('teacher.monitoring.show');

    // Interstitial Screen Confirm Dialog: Session global kill checklist verification view step
    Route::get('/teacher/monitoring/end-confirmation', [TeacherController::class, 'endExamConfirmation'])->name('teacher.monitoring.endConfirmation');

    // Post-Exam Phase Summary: Compiles final examination response curves metrics insights
    Route::get('/teacher/monitoring/session-ended', [TeacherController::class, 'examSessionEnded'])->name('teacher.exam.endedOverview');

    // Endpoint Command Action to instantly force-terminate an open runtime examination session
    Route::post('/teacher/monitoring/end-exam', [TeacherController::class, 'endExamSession'])->name('teacher.monitoring.endExam');

    // Streams compiled raw tracking incident logs downstream context as static sheet formatting (.csv)
    Route::get('/teacher/monitoring/export-log', [TeacherController::class, 'exportSessionLog'])->name('teacher.monitoring.exportLog');

    /* --- 📝 INTERACTIVE MANUAL GRADING SUBSYSTEM WORKFLOW (Delegated to dedicated Controller) --- */
    Route::get('/teacher/grading/{student_id}', [GradingController::class, 'show'])->name('teacher.grading.show');
    Route::post('/teacher/grading/{student_id}/save', [GradingController::class, 'store'])->name('teacher.grading.store');
    Route::get('/teacher/grading/{student_id}/success', [GradingController::class, 'success'])->name('teacher.grading.success');
    
    /* --- 📂 SECURE ARCHIVAL FILE SUBMISSIONS STREAMER --- */
    // Overrides permission locks to explicitly resolve 403 authorization barriers by using protected stream downloads
    Route::get('/teacher/submissions/download/{filename}', function ($filename) {
        $path = 'submissions/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'The requested submission item could not be located inside database storage partitions.');
        }

        return response()->file(Storage::disk('public')->path($path));
    })->name('teacher.submissions.download');

    // 4. Nested Question Banks & Course Structures Configurations Namespace Tree Prefix
    Route::prefix('teacher')->group(function () {
        
        // Master Question Storage Database Collection List View Index
        Route::get('/question-bank', [TeacherController::class, 'questionBank'])->name('teacher.question-bank');
        Route::get('/courses', [TeacherController::class, 'myCourses'])->name('teacher.courses');
        Route::post('/exams', [TeacherController::class, 'createExam'])->name('exams.store');
        
        // Render empty creation panel context worksheet
        Route::get('/questions/create', function() {
            return view('teacher.create_question'); 
        })->name('questions.create');

        // Capture question schema details and log to data entities structures
        Route::post('/questions', [TeacherController::class, 'addQuestion'])->name('questions.store');
        
        // Load target instance variables fields to form textboxes for updates
        Route::get('/questions/{id}/edit', [TeacherController::class, 'editQuestion'])->name('questions.edit');

        // Process modifications changes requests and patch existing relational model attributes
        Route::put('/questions/{id}', [TeacherController::class, 'updateQuestion'])->name('questions.update');

        // Permanently wipe out a question matrix map template item instance entity row
        Route::delete('/questions/{id}', [TeacherController::class, 'destroyQuestion'])->name('questions.destroy');
        
        // Pull down list tracking of student examination attempts logs arrays
        Route::get('/exams/{examId}/submissions', [TeacherController::class, 'submissions'])->name('exams.submissions');
    });
});

/* --- 🎓 STUDENT PORTAL SEGMENT ONLY INTERFACES --- */
Route::middleware(['auth', 'role:student'])->group(function () {
    
    // 1. Primary Student Landing Dashboard Grid (Populates metric balances data values)
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    
    // 2. Profile Management and Metadata Update Action Elements
    Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    Route::post('/student/settings/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    
    // 3. Evaluated Collections and Curriculums Matrix Relational Maps endpoints
    Route::get('/student/exams', [StudentController::class, 'exams'])->name('student.exams');
    // History subroute to pull tracking list entries of previous student examination loops
    Route::get('/student/history', [StudentController::class, 'mySubmissions'])->name('student.history');

    // 4. Helpdesk Support Center Template view link
    Route::get('/student/support', [StudentController::class, 'support'])->name('student.support');
    // Real-time asynchronous support ticket generation parameter interceptor route
    Route::post('/student/support', [StudentController::class, 'storeSupportTicket'])->name('student.support.store');
    
    /* --- 📐 PRINT-READY SECURE REGISTRATION KEY GENERATOR --- */
    // Triggered cleanly by clicking 'Download Hall Ticket' link on the student dashboard interface card
    Route::get('/student/print-ticket', [StudentController::class, 'printHallTicket'])->name('student.printTicket');

    /* --- 🔑 SINGLE-USE EXAM ACCESS CODE VALIDATION ROUTE --- */
    // Evaluates incoming classroom authorization tokens before unlocking the verification hardware panel check
    Route::post('/student/verify-code', [StudentController::class, 'enterProctorRoom'])->name('student.verifyCode');

    /* --- 🛡️ SYSTEM INTEGRITY CHECK / REAL-TIME PROCTORING ENTRANCE --- */
    // Target gate layout template served after successful access key verification routines
    Route::get('/student/exam/verification', function () {
        return view('student.exam-room');
    })->name('student.exam.verification');
});

/* --- 👑 ADMINISTRATIVE CONTROL SEGMENT INTERFACES --- */
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    
    // 1. Main Admin Dashboard View Link (Calculates dynamic layout metric states)
    Route::get('/admin/dashboard', function () {
        $totalUsers = \App\Models\User::count(); 

        $activeExams = \App\Models\Exam::where('status', 'published')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();

        $cpuUsage = rand(12, 45); 

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

        if (class_exists('\App\Models\AuditLog')) {
            $systemLogs = \App\Models\AuditLog::orderBy('created_at', 'desc')->take(5)->get();
        } else {
            $systemLogs = collect([]);
        }

        return view('admin.dashboard', compact('totalUsers', 'activeExams', 'cpuUsage', 'proctorFlags', 'systemLogs')); 
    })->name('admin.dashboard');

    // 2. Automated Server Relational Database Storage Backups Dispatch Controller Route
    Route::get('/admin/backup', function () {
        return view('admin.backup');
    })->name('admin.backup');

    // 3. Central System Global Strategy Properties Adjustment Form Worksheet Panel
    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
});