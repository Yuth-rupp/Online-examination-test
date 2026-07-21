<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\ProctorHandshakeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Models\Exam;

/*
|--------------------------------------------------------------------------
| Web Routes Matrix
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        switch ($role) {
            case 'super_admin': return redirect()->route('superadmin.dashboard');
            case 'admin':       return redirect()->route('admin.dashboard');
            case 'teacher':     return redirect()->route('teacher.dashboard');
            case 'student':     return redirect()->route('student.dashboard');
            default:            return redirect()->route('login.page');
        }
    }
    return redirect()->route('login.page');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () { return view('auth.login'); })->name('login.page');
    Route::get('/auth/login', function () { return redirect()->route('login.page'); });
    Route::get('/register', function () { return view('auth.register'); })->name('register.page');
    Route::get('/register/success', function () {
        if (!session()->has('registered_email')) { return redirect()->route('register.page'); }
        return view('auth.register_success');
    })->name('register.success');
    Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/forgot-password/success', function () {
        if (!session()->has('reset_email')) { return redirect()->route('password.request'); }
        return view('auth.status-email');
    })->name('password.success');
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login',    [AuthController::class, 'login'])->name('login');
    });
    Route::get('/superadmin/login',           function () { return view('auth.superadmin-login'); })->name('superadmin.login.page');
    Route::post('/superadmin/login',          [AuthController::class, 'sendSuperAdminCode'])->name('superadmin.sendcode');
    Route::get('/superadmin/verify',          function () { return view('auth.superadmin-verify'); })->name('superadmin.verify.page');
    Route::post('/superadmin/verify',         [AuthController::class, 'verifySuperAdminCode'])->name('superadmin.verify');
    Route::get('/superadmin/forgot-password', function () { return view('auth.superadmin-forgot-password'); })->name('superadmin.password.request');
});

Route::middleware(['auth'])->post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

/* =========================================================================
 | TEACHER WORKSPACE ROUTES
 | =========================================================================
*/
Route::middleware(['auth', 'role:teacher'])->group(function () {

    Route::get('/teacher/dashboard',  [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/analytics',  [TeacherController::class, 'analytics'])->name('teacher.analytics');
    Route::get('/teacher/settings',   function () { return view('teacher.settings'); })->name('teacher.settings');
    Route::post('/teacher/settings',  [TeacherController::class, 'updateSettings'])->name('teacher.settings.update');
    Route::post('/teacher/settings/password', [TeacherController::class, 'updatePassword'])->name('teacher.settings.update.password');
    
    // Asynchronous avatar upload handler
    Route::post('/teacher/settings/avatar', [TeacherController::class, 'updateAvatar'])->name('teacher.settings.avatar');

    // Real-time Teacher Notification Endpoints
    Route::get('/teacher/notifications', [NotificationController::class, 'index'])->name('teacher.notifications');
    Route::post('/teacher/notifications/clear', [NotificationController::class, 'clearAll'])->name('teacher.notifications.clear');

    Route::get('/teacher/courses/create',    [TeacherController::class, 'createCourse'])->name('teacher.courses.create');
    Route::post('/teacher/courses/store',    [TeacherController::class, 'storeCourse'])->name('teacher.courses.store');
    Route::delete('/teacher/courses/{id}',   [TeacherController::class, 'destroyCourse'])->name('teacher.courses.destroy');
    Route::get('/teacher/exams/{id}/preview',[TeacherController::class, 'previewExam'])->name('teacher.exams.preview');

    // ── TEACHER MONITORING (for watching student webcam feeds) ─────────────
    Route::get('/teacher/monitoring',
        function () { return view('teacher.monitoring'); }
    )->name('teacher.monitoring.show');

    // HTTP polling endpoint — returns pending proctor keys from cache.
    Route::get('/teacher/monitoring/pending-keys',
        [ProctorHandshakeController::class, 'getPendingKeys']
    )->name('teacher.monitoring.pending-keys');

    Route::post('/teacher/monitoring/approve-proctor-key',
        [ProctorHandshakeController::class, 'approveKey']
    )->name('teacher.monitoring.approveKey');

    Route::get('/teacher/monitoring/end-confirmation',
        [TeacherController::class, 'endExamConfirmation']
    )->name('teacher.monitoring.endConfirmation');

    Route::get('/teacher/monitoring/session-ended',
        [TeacherController::class, 'examSessionEnded']
    )->name('teacher.exam.endedOverview');

    Route::post('/teacher/monitoring/end-exam',
        [TeacherController::class, 'endExamSession']
    )->name('teacher.monitoring.endExam');

    Route::get('/teacher/monitoring/export-log',
        [TeacherController::class, 'exportSessionLog']
    )->name('teacher.monitoring.exportLog');
    // ── END TEACHER MONITORING ─────────────────────────────────────────────

    Route::get('/teacher/grading',                  [GradingController::class, 'queueIndex'])->name('teacher.grading.queue');
    Route::get('/teacher/grading/evaluate/{student_id}', [GradingController::class, 'show'])->name('teacher.grading.show');
    Route::post('/teacher/grading/store/{submission_id}',[GradingController::class, 'store'])->name('teacher.grading.store');
    Route::get('/teacher/grading/success/{submission_id}',[GradingController::class, 'success'])->name('teacher.grading.success');

    Route::get('/teacher/submissions/download/{filename}', function ($filename) {
        $path = 'submissions/' . $filename;
        if (!Storage::disk('public')->exists($path)) { abort(404, 'Submission item not located.'); }
        return response()->file(Storage::disk('public')->path($path));
    })->name('teacher.submissions.download');

    Route::prefix('teacher')->group(function () {
        Route::get('/question-bank',          [TeacherController::class, 'questionBank'])->name('teacher.question-bank');
        Route::get('/courses',                [TeacherController::class, 'myCourses'])->name('teacher.courses');
        Route::post('/exams',                 [TeacherController::class, 'createExam'])->name('exams.store');
        Route::post('/exams/api-create',      [TeacherController::class, 'storeApiExam'])->name('exams.api-create');
        Route::post('/settings/update-shuffle',[TeacherController::class, 'updateShuffle'])->name('settings.update-shuffle');
        Route::get('/questions/create',       [TeacherController::class, 'createQuestion'])->name('questions.create');
        Route::post('/questions',             [TeacherController::class, 'addQuestion'])->name('questions.store');
        Route::get('/questions/{id}/edit',    [TeacherController::class, 'editQuestion'])->name('questions.edit');
        Route::put('/questions/{id}',         [TeacherController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{id}',      [TeacherController::class, 'destroyQuestion'])->name('questions.destroy');
        Route::get('/exams/{examId}/submissions',[TeacherController::class, 'submissions'])->name('exams.submissions');
    });
});

/* =========================================================================
 | STUDENT WORKSPACE ROUTES
 | =========================================================================
*/
Route::middleware(['auth', 'role:student'])->group(function () {

    Route::get('/student/dashboard',              [StudentController::class, 'index'])->name('student.dashboard');
    Route::get('/student/settings',               [StudentController::class, 'settings'])->name('student.settings');
    Route::post('/student/settings/update',       [StudentController::class, 'updateProfile'])->name('student.profile.update');
    Route::post('/student/settings/photo',        [StudentController::class, 'uploadProfilePhoto'])->name('student.profile.photo');
    Route::get('/student/exams',                  [StudentController::class, 'exams'])->name('student.exams');
    Route::get('/student/history',                [StudentController::class, 'mySubmissions'])->name('student.history');
    Route::post('/student/history',               [StudentController::class, 'storeExamSubmission'])->name('student.submission.store');
    Route::post('/student/exams/log-violation',   [StudentController::class, 'logProctorViolation'])->name('student.exams.logViolation');

    // ── STUDENT PROCTORING ──────────────────────────────────────────────────
    Route::post('/student/exams/register-proctor-key',
        [ProctorHandshakeController::class, 'registerKey']
    )->name('student.exams.registerProctorKey');

    Route::post('/student/exams/stream-frame',
        [ProctorHandshakeController::class, 'streamProctorFrame']
    )->name('student.exams.streamFrame');
    // ── END STUDENT PROCTORING ──────────────────────────────────────────────

    // ── REAL-TIME NOTIFICATIONS (used by dashboard, exams, history, settings) ──
    Route::get('/student/notifications',               [NotificationController::class, 'index'])->name('student.notifications');
    Route::get('/student/notifications/unread-count',  [NotificationController::class, 'unreadCount'])->name('student.notifications.unreadCount');
    Route::post('/student/notifications/{id}/read',    [NotificationController::class, 'markRead'])->name('student.notifications.markRead');
    Route::post('/student/notifications/read-all',     [NotificationController::class, 'markAllRead'])->name('student.notifications.markAllRead');
    Route::post('/student/notifications/clear',        [NotificationController::class, 'clearAll'])->name('student.notifications.clear');

    Route::get('/student/support',                [StudentController::class, 'support'])->name('student.support');
    Route::post('/student/support',               [StudentController::class, 'storeSupportTicket'])->name('student.support.store');
    Route::get('/student/support/notifications',  [StudentController::class, 'pollSupportNotifications'])->name('student.support.notifications');
    Route::get('/student/support/confirm',        function () { return view('student.confirm_support'); })->name('student.support.confirm');
    Route::get('/student/print-ticket',           [StudentController::class, 'printHallTicket'])->name('student.printTicket');
    Route::post('/student/verify-code',           [StudentController::class, 'enterProctorRoom'])->name('student.verifyCode');

    Route::get('/student/exam/verification', function (Request $request) {
        $exams = Exam::with('course')->where('status', 'published')->get();
        $exam  = Exam::where('access_code', strtoupper(trim($request->query('code'))))->first();
        return view('student.exam-room', compact('exams', 'exam'));
    })->name('student.exam.verification');

    Route::get('/student/exams/success/{id}', [StudentController::class, 'showExamSuccess'])->name('student.exams.success');
    Route::get('/student/exams/{id}',         [StudentController::class, 'showExamDetails'])->name('exams.show');
    Route::get('/student/exams/{id}/enter',   [StudentController::class, 'enterExamInterface'])->name('exams.enter');
    Route::get('/student/exams/{id}/start',   [StudentController::class, 'startExamSession'])->name('exams.start');
    Route::get('/student/review-feedback/{id}',[StudentController::class, 'viewExamFeedback'])->name('exams.feedback');
});

/* =========================================================================
 | SUPER ADMIN ROUTES
 | =========================================================================
*/
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->group(function () {

    Route::get('/dashboard',            [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/telemetry/live-feed',  [SuperAdminController::class, 'getLiveActivityFeedApi'])->name('superadmin.telemetry.livefeed');

    // Super Admin Monitoring (watches teacher/system activity — NOT student webcams)
    Route::get('/monitoring',           [SuperAdminController::class, 'monitoring'])->name('superadmin.monitoring.index');
    Route::get('/monitoring/teachers',  [SuperAdminController::class, 'teachersMonitoringApi'])->name('superadmin.monitoring.teachers');
    Route::get('/monitoring/api',       [SuperAdminController::class, 'getMonitoringStatsApi'])->name('superadmin.monitoring.api');

    Route::get('/exams',                [SuperAdminController::class, 'exams'])->name('superadmin.exams.index');
    Route::get('/exams/api',            [SuperAdminController::class, 'getExamsDataApi'])->name('superadmin.exams.api');
    Route::post('/exams/{id}/force-end',[SuperAdminController::class, 'forceEndExam'])->name('superadmin.exams.forceEnd');

    Route::get('/reports',              [SuperAdminController::class, 'reports'])->name('superadmin.reports.index');
    Route::get('/reports/api',          [SuperAdminController::class, 'getReportsAnalyticsDataApi'])->name('superadmin.reports.api');
    Route::get('/reports/departments',  [SuperAdminController::class, 'getReportsDepartmentDataApi'])->name('superadmin.reports.departments');
    Route::get('/reports/live',         [SuperAdminController::class, 'getReportsLiveCountersApi'])->name('superadmin.reports.live');
    Route::get('/backups',              [SuperAdminController::class, 'backups'])->name('superadmin.backups.index');
    Route::post('/backups/trigger',     [SuperAdminController::class, 'updateSettings'])->name('superadmin.backup.trigger');
    Route::post('/backups/{id}/restore',[SuperAdminController::class, 'forceEndExam']);
    Route::get('/backup-api-stream',    [SuperAdminController::class, 'getLiveActivityFeedApi'])->name('superadmin.backup.api');
    Route::get('/audit-logs',           [SuperAdminController::class, 'auditLogs'])->name('superadmin.audit-logs.index');
    Route::get('/audit-logs/export',    [SuperAdminController::class, 'exportAuditLogsCsv'])->name('superadmin.audit-logs.export');

    Route::get('/settings',                    [SuperAdminController::class, 'settings'])->name('superadmin.settings.index');
    Route::post('/settings',                   [SuperAdminController::class, 'updateSettings'])->name('superadmin.settings.update');
    Route::post('/settings/smtp-test',         [SuperAdminController::class, 'testSmtpConnectionApi'])->name('superadmin.settings.smtp.test');
    Route::post('/settings/clear-cache',       [SuperAdminController::class, 'clearDatabaseCache'])->name('superadmin.settings.clearCache');
    Route::post('/settings/optimize-db',       [SuperAdminController::class, 'optimizeDatabaseTables'])->name('superadmin.settings.optimizeDb');
    Route::post('/settings/clear-logs',        [SuperAdminController::class, 'clearSystemLogs'])->name('superadmin.settings.clearLogs');
    Route::post('/settings/flush-queue',       [SuperAdminController::class, 'flushProctoringQueue'])->name('superadmin.settings.flushQueue');
    Route::post('/settings/purge-audit-logs',  [SuperAdminController::class, 'purgeSystemAuditLogs'])->name('superadmin.settings.purgeAuditLogs');

    Route::get('/admins',                      [SuperAdminController::class, 'adminIndex'])->name('superadmin.admins.index');
    Route::get('/admins/api-stream',           [SuperAdminController::class, 'adminApiIndex'])->name('superadmin.admins.api');
    Route::post('/admins/store',               [SuperAdminController::class, 'adminStore'])->name('superadmin.admins.store');
    Route::patch('/admins/{id}/toggle-status', [SuperAdminController::class, 'adminToggleStatus'])->name('superadmin.admins.toggleStatus');
    Route::patch('/admins/{id}/change-role',   [SuperAdminController::class, 'adminChangeRole'])->name('superadmin.admins.changeRole');
});

/* =========================================================================
 | ADMIN ROUTES
 | =========================================================================
*/
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {

    Route::get('/admin/dashboard',              [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/telemetry-api',[AdminController::class, 'getTelemetryApi'])->name('admin.dashboard.api');

    Route::get('/admin/notifications',              [NotificationController::class, 'index'])->name('admin.notifications');
    Route::get('/admin/notifications/unread-count',  [NotificationController::class, 'unreadCount'])->name('admin.notifications.unreadCount');
    Route::post('/admin/notifications/{id}/read',    [NotificationController::class, 'markRead'])->name('admin.notifications.markRead');
    Route::post('/admin/notifications/read-all',     [NotificationController::class, 'markAllRead'])->name('admin.notifications.markAllRead');
    Route::post('/admin/notifications/clear',        [NotificationController::class, 'clearAll'])->name('admin.notifications.clear');

    Route::get('/admin/exams',                  [AdminController::class, 'examWorkspace'])->name('admin.exams');
    Route::post('/admin/exams/store',           [AdminController::class, 'storeExam'])->name('admin.exams.store');

    Route::get('/admin/users',                  [AdminController::class, 'userManagement'])->name('admin.users');
    Route::post('/admin/users/store',           [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}/update',      [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}',          [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::get('/admin/users/export-csv',       [AdminController::class, 'exportUsersCsv'])->name('admin.users.export');
    Route::put('/admin/users/{id}/force-password',[AdminController::class, 'forceResetPassword'])->name('admin.users.forcePassword');
    Route::patch('/admin/users/{id}/toggle-status',[AdminController::class, 'toggleUserStatus'])->name('admin.users.toggleStatus');

    Route::get('/admin/security',               [AdminController::class, 'securityLogWorkspace'])->name('admin.security');
    Route::get('/admin/security/telemetry-stream',[AdminController::class, 'getSecurityTelemetryApi'])->name('admin.security.api');

    Route::get('/admin/support',                [AdminController::class, 'supportTicketWorkspace'])->name('admin.support');
    Route::get('/admin/support/telemetry-stream',[AdminController::class, 'getSupportTicketTelemetryApi'])->name('admin.support.api');
    Route::get('/admin/support/{id}/ticket-review',[AdminController::class, 'reviewTicketForm'])->name('admin.support.review');
    Route::post('/admin/support/{id}/resolve',  [AdminController::class, 'resolveSupportTicket'])->name('admin.support.resolve');

    Route::get('/admin/backup',                 [AdminController::class, 'backupSettings'])->name('admin.backup');
    Route::get('/admin/backup/telemetry-stream',[AdminController::class, 'getBackupHistoryTelemetryApi'])->name('admin.backup.api');
    Route::post('/admin/backup/settings-update',[AdminController::class, 'updateBackupSettings'])->name('admin.backup.settings.update');
    Route::post('/admin/backup/trigger',        [AdminController::class, 'triggerManualBackup'])->name('admin.backup.trigger');
    Route::get('/admin/backup/download/{filename}',[AdminController::class, 'downloadBackupFile'])->name('admin.backup.download');
    Route::delete('/admin/backup/delete/{filename}',[AdminController::class, 'deleteBackupFile'])->name('admin.backup.delete');

    Route::get('/admin/settings',               [AdminController::class, 'settingsWorkspace'])->name('admin.settings');
    Route::post('/admin/settings/update-rules', [AdminController::class, 'updateSystemRules'])->name('admin.settings.rules');
    Route::post('/admin/settings/update-profile',[AdminController::class, 'updateAdminProfile'])->name('admin.settings.profile');
    Route::get('/admin/settings/password',      [AdminController::class, 'passwordWorkspace'])->name('admin.settings.password');
    Route::post('/admin/settings/clear-cache',  [AdminController::class, 'clearDatabaseCache'])->name('admin.settings.clearCache');
    Route::post('/admin/settings/optimize-db',  [AdminController::class, 'optimizeDatabase'])->name('admin.settings.optimizeDb');
    Route::post('/admin/settings/clear-logs',   [AdminController::class, 'clearLogs'])->name('admin.settings.clearLogs');
    Route::post('/admin/settings/flush-queue',  [AdminController::class, 'flushProctoringQueue'])->name('admin.settings.flushQueue');
});