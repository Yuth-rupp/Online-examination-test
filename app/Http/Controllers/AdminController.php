<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Exam;
use App\Models\Submission;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Support\InstitutionalIdGenerator;

class AdminController extends Controller
{
    /**
     * The department IDs the currently signed-in admin is allowed to see.
     *
     *  - super_admin  -> [] (empty array means "no restriction" everywhere
     *                    this is used, via User/Course/Exam::inDepartments())
     *  - department admin (users.department_id is set) -> [that one ID]
     *  - a legacy/global admin with no department_id set -> [] (kept
     *    unrestricted on purpose, so existing installs don't break the
     *    moment this migration runs — a superadmin can assign them a
     *    department later from Super Admin > Admins)
     */
    private function scopedDepartmentIds(): array
    {
        $user = Auth::user();

        if (!$user || $user->role === 'super_admin') {
            return [];
        }

        return $user->managedDepartmentIds();
    }

    /**
     * Private helper method to log real-time security anomalies and audit events.
     */
    private function logSecurityEvent($userId, $action, $target, $summary)
    {
        DB::table('audit_logs')->insert([
            'user_id'    => $userId,
            'action'     => $action,
            'payload'    => json_encode([
                'target_title' => $target,
                'summary'      => $summary
            ]),
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);
    }

    /**
     * Private helper to read diagnostic server process footprints.
     */
    /**
     * Real container CPU usage, not the old sys_getloadavg()/4 guess.
     *
     * Why the old version was broken: sys_getloadavg() returns the machine's
     * Unix load average (queued processes), not a CPU %, and on a shared
     * host like Railway it often reflects OTHER tenants' load too — dividing
     * that by an assumed "4 cores" produced nonsense numbers like 379.9%.
     *
     * This reads the actual cgroup CPU accounting for THIS container
     * (v2 first, v1 fallback) and computes real usage as a delta between two
     * samples — i.e. "how much of its allocated CPU did this container use
     * between the last check and now" — which is what "real-time load" means.
     */
    private function getSystemLoadPercentage()
    {
        try {
            $usageUsec = $this->readCgroupCpuUsageUsec();
            $cores     = $this->readCgroupAllocatedCores();

            if ($usageUsec !== null && $cores !== null) {
                return $this->cpuPercentFromUsageSample($usageUsec, $cores);
            }
        } catch (\Throwable $e) {
            // Fall through to the load-average fallback below (e.g. cgroups
            // unavailable — local Windows/macOS dev environment).
        }

        if (function_exists('sys_getloadavg')) {
            $load  = \sys_getloadavg();
            $cores = (float) (trim((string) @shell_exec('nproc')) ?: 4);
            return isset($load[0]) ? round(min(100, $load[0] * 100 / max(0.5, $cores)), 1) : 0.0;
        }

        return 0.0;
    }

    /**
     * Turns a cumulative CPU-usage reading into a real percentage by diffing
     * it against the last sample taken (cached). This is what makes the
     * number "real-time" — it reflects actual usage in the interval since
     * the last poll, not a smoothed multi-minute average.
     */
    private function cpuPercentFromUsageSample(float $usageUsec, float $cores): float
    {
        $cacheKey = 'system.cpu.last_sample';
        $now      = microtime(true);
        $prev     = Cache::get($cacheKey);

        if (!$prev) {
            // Cold start (first request in 5+ minutes) — nothing to diff
            // against yet. Take a real 150ms synchronous second sample so we
            // still return an accurate number instead of a fake placeholder.
            usleep(150000);
            $usageUsec2 = $this->readCgroupCpuUsageUsec() ?? $usageUsec;
            Cache::put($cacheKey, ['usage' => $usageUsec2, 'time' => microtime(true)], 300);

            $deltaUsageUsec = max(0, $usageUsec2 - $usageUsec);
            $percent = ($deltaUsageUsec / 150000) / max(0.01, $cores) * 100;
            return round(max(0, min(100, $percent)), 1);
        }

        Cache::put($cacheKey, ['usage' => $usageUsec, 'time' => $now], 300);

        $deltaUsageUsec = $usageUsec - $prev['usage'];
        $deltaTimeUsec  = ($now - $prev['time']) * 1_000_000;

        if ($deltaTimeUsec <= 0) {
            return 0.0;
        }

        $percent = ($deltaUsageUsec / $deltaTimeUsec) / max(0.01, $cores) * 100;
        return round(max(0, min(100, $percent)), 1);
    }

    /** Cumulative CPU time (microseconds) this container has used — cgroup v2, falling back to v1. */
    private function readCgroupCpuUsageUsec(): ?float
    {
        if (is_readable('/sys/fs/cgroup/cpu.stat')) {
            $stat = @file_get_contents('/sys/fs/cgroup/cpu.stat');
            if ($stat && preg_match('/^usage_usec\s+(\d+)/m', $stat, $m)) {
                return (float) $m[1];
            }
        }

        if (is_readable('/sys/fs/cgroup/cpuacct/cpuacct.usage')) {
            $nsec = @file_get_contents('/sys/fs/cgroup/cpuacct/cpuacct.usage');
            if ($nsec !== false) {
                return ((float) trim($nsec)) / 1000; // nanoseconds → microseconds
            }
        }

        return null;
    }

    /** How many CPU cores this container is actually allocated (its real ceiling, not the host's). */
    private function readCgroupAllocatedCores(): ?float
    {
        $hostCores = (float) (trim((string) @shell_exec('nproc')) ?: 4);

        if (is_readable('/sys/fs/cgroup/cpu.max')) {
            $raw = trim((string) @file_get_contents('/sys/fs/cgroup/cpu.max'));
            [$quota, $period] = array_pad(explode(' ', $raw), 2, null);
            if ($quota && $quota !== 'max' && $period) {
                return max(0.01, ((float) $quota) / ((float) $period));
            }
            return $hostCores; // no quota set — container can use up to the host's cores
        }

        if (is_readable('/sys/fs/cgroup/cpu/cpu.cfs_quota_us') && is_readable('/sys/fs/cgroup/cpu/cpu.cfs_period_us')) {
            $quota  = (float) trim((string) @file_get_contents('/sys/fs/cgroup/cpu/cpu.cfs_quota_us'));
            $period = (float) trim((string) @file_get_contents('/sys/fs/cgroup/cpu/cpu.cfs_period_us'));
            if ($quota > 0 && $period > 0) {
                return max(0.01, $quota / $period);
            }
            return $hostCores;
        }

        return null;
    }

    /**
     * Compute the live dashboard metrics straight from the database —
     * no placeholder/demo numbers. A brand-new install with no activity
     * yet will correctly show zeros everywhere instead of fake data.
     */
    private function getDashboardMetrics()
    {
        $deptIds = $this->scopedDepartmentIds();

        $managedUsers = User::inDepartments($deptIds)->count();
        $newUsersThisWeek = User::inDepartments($deptIds)->where('created_at', '>=', now()->startOfWeek())->count();

        $activeExams = Exam::inDepartments($deptIds)
            ->where('status', 'published')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();

        $upcomingExams = Exam::inDepartments($deptIds)
            ->where('status', 'published')
            ->where('start_time', '>', now())
            ->count();

        $closedExams = Exam::inDepartments($deptIds)
            ->where('status', 'published')
            ->where('end_time', '<', now())
            ->count();

        $examsEndingToday = Exam::inDepartments($deptIds)->whereBetween('end_time', [now()->startOfDay(), now()->endOfDay()])->count();

        // Support tickets and submissions aren't department-scoped in this
        // schema yet (tickets don't carry a department/course reference),
        // so they stay global for now — every admin sees all of them.
        $openTickets = DB::table('support_tickets')->where('status', '!=', 'resolved')->count();
        $urgentTickets = DB::table('support_tickets')->where('status', '!=', 'resolved')->where('priority', 'urgent')->count();

        $submissionsToday = Submission::whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();

        // Real submissions per day for the current week (Mon → Sun),
        // not a hardcoded demo array. Fresh accounts simply see zeros.
        $weekStart = now()->startOfWeek();
        $weeklySubmissions = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weeklySubmissions[] = Submission::whereNotNull('submitted_at')
                ->whereBetween('submitted_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
                ->count();
        }

        return compact(
            'managedUsers',
            'newUsersThisWeek',
            'activeExams',
            'upcomingExams',
            'closedExams',
            'examsEndingToday',
            'openTickets',
            'urgentTickets',
            'submissionsToday',
            'weeklySubmissions'
        );
    }

    /**
     * Display the main administrator dashboard interface workspace.
     */
    public function index()
    {
        $metrics = $this->getDashboardMetrics();

        $myLogs = DB::table('audit_logs')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', array_merge($metrics, compact('myLogs')));
    }

    /**
     * Live telemetry feed polled every few seconds by the dashboard so
     * metric cards, the weekly chart, and the status donut update in
     * real time without a full page reload.
     */
    public function getTelemetryApi()
    {
        return response()->json($this->getDashboardMetrics());
    }

    /**
     * Build the live exam list + summary stat cards straight from the
     * database. No placeholder numbers — a freshly registered account
     * with no activity yet correctly shows 0 enrolled / 0 submissions
     * instead of the old hardcoded "45 students, 12 submitted" demo data.
     *
     * This now powers a read-only "Department Schedule & Monitor" view:
     * admins watch live status, which teacher/course an exam belongs to,
     * and the submission count out of that course's actual enrollment —
     * they no longer create or edit exams from here (that stays with
     * the teacher who owns the course).
     */
    private function getExamWorkspaceData()
    {
        $deptIds = $this->scopedDepartmentIds();

        $exams = Exam::inDepartments($deptIds)
            ->with('course', 'creator')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($exam) {
                $isPublished = $exam->status === 'published';

                $isActive = $isPublished
                    && $exam->start_time
                    && $exam->end_time
                    && now()->between($exam->start_time, $exam->end_time);

                $status = $isActive ? 'active' : ($isPublished ? 'closed' : 'draft');

                $submitted = Submission::where('exam_id', $exam->exam_id)
                    ->whereNotNull('submitted_at')
                    ->count();

                // Total enrolled = the actual roster of that exam's course,
                // not the whole department's student count. Falls back to
                // 0 when the exam has no course assigned yet.
                $enrolled = $exam->course_id
                    ? Enrollment::where('course_id', $exam->course_id)
                        ->where('status', 'active')
                        ->distinct('user_id')
                        ->count('user_id')
                    : 0;

                $questionCount = $exam->questions()->count();

                $instructor = $exam->creator->full_name ?? null;
                $instructorInitials = $instructor
                    ? collect(explode(' ', $instructor))->take(2)->map(fn($p) => strtoupper($p[0] ?? ''))->join('')
                    : null;

                return [
                    'id' => $exam->exam_id,
                    'title' => $exam->title,
                    'subject' => $exam->course->name ?? 'No course assigned',
                    'status' => $status,
                    'students' => $enrolled,
                    'submitted' => $submitted,
                    'closes' => $exam->end_time ?: 'Not scheduled yet',
                    'instructor' => $instructor,
                    'instructor_initials' => $instructorInitials ?: 'AD',
                    'questions' => $questionCount,
                    'sections' => [['name' => 'Section 1', 'duration' => $exam->duration ?? 60]],
                ];
            });

        $stats = [
            'active'            => $exams->where('status', 'active')->count(),
            'draft'             => $exams->where('status', 'draft')->count(),
            'closed'            => $exams->where('status', 'closed')->count(),
            'totalSubmissions'  => Submission::whereNotNull('submitted_at')
                                        ->whereIn('exam_id', $exams->pluck('id'))
                                        ->count(),
        ];

        return [
            'exams' => $exams->values()->toArray(),
            'stats' => $stats,
        ];
    }

    /**
     * Display and manage the Admin Workspace for Exams panel.
     */
    public function examWorkspace()
    {
        $openTickets = DB::table('support_tickets')->where('status', '!=', 'resolved')->count();

        $data = $this->getExamWorkspaceData();

        // Lets the view show a "Data Science"-style department badge next
        // to the page title when a department-scoped admin is looking at
        // it, same treatment as the Security Audit Center below.
        $actor = Auth::user();
        $isDepartmentAdmin = $actor->isDepartmentAdmin();
        $departmentName = $isDepartmentAdmin ? optional($actor->department)->name : null;

        return view('admin.exams', array_merge($data, compact('openTickets', 'isDepartmentAdmin', 'departmentName')));
    }

    /**
     * Live polling endpoint for the Exams workspace — hit every few
     * seconds by the front-end so stat cards and exam participation
     * numbers update in real time (new sign-ups, new submissions,
     * newly-published exams) without a manual page refresh.
     */
    public function getExamsDataApi()
    {
        return response()->json($this->getExamWorkspaceData());
    }

    /**
     * Display directory workspace listings for user profiles management.
     */
    public function userManagement(Request $request)
    {
        $deptIds = $this->scopedDepartmentIds();

        $totalUsers = User::inDepartments($deptIds)->count();
        $activeExams = Exam::inDepartments($deptIds)
            ->where('status', 'published')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();

        $cpuUsage = $this->getSystemLoadPercentage();

        $query = User::query()
            ->where('user_id', '!=', Auth::id())
            ->where('role', '!=', 'super_admin')
            ->inDepartments($deptIds);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $isDepartmentAdmin = Auth::user()->isDepartmentAdmin();
        $departments = $isDepartmentAdmin ? collect() : \App\Models\Department::orderBy('name')->get(['id', 'name']);

        return view('admin.users', compact('totalUsers', 'activeExams', 'cpuUsage', 'users', 'isDepartmentAdmin', 'departments'));
    }

    /**
     * Store and register a newly generated application directory profile.
     */
    public function storeUser(Request $request)
    {
        $actor = Auth::user();
        $isDepartmentAdmin = $actor->isDepartmentAdmin();

        // A department admin can only add teachers/students into their own
        // department — creating another admin is a super_admin-only action
        // (done from Super Admin > Admins, then assigned to a department).
        $allowedRoles = $isDepartmentAdmin ? 'teacher,student' : 'admin,teacher,student';

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|string|in:' . $allowedRoles,
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // A department admin's new users always land in their own
        // department, regardless of what was posted. A global (super)
        // admin may optionally pick a department from the form.
        $departmentId = $isDepartmentAdmin ? $actor->department_id : ($validated['department_id'] ?? null);

        $newUser = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password_hash' => Hash::make($validated['password']),
            'status' => 'active',
            'department_id' => $departmentId,
            'institutional_id' => InstitutionalIdGenerator::generate($validated['role']),
        ]);

        // If they were created as a teacher with a department, also link
        // them into that department's teaching roster so they immediately
        // show up in Department > Teachers (and can be added to further
        // departments later without touching this home assignment).
        if ($newUser->role === 'teacher' && $departmentId) {
            $newUser->departments()->syncWithoutDetaching([$departmentId]);
        }

        $this->logSecurityEvent(Auth::id(), 'uploaded', 'User Directory', 'Compiled new application profile space for ' . $newUser->full_name);

        return redirect()->route('admin.users')->with('success', 'User profile compiled successfully.');
    }

    /**
     * Update an existing user's profile details and, critically, their
     * department assignment. This is the endpoint that actually moves a
     * teacher or student into the department an admin is responsible for
     * (e.g. "Data Science" vs "ITE") after the account already exists.
     *
     *  - A department admin can only edit users already inside their own
     *    department (enforced by inDepartments() below), and the
     *    department itself stays pinned to their own — they can't move
     *    a user OUT to a department they don't manage.
     *  - A global/super-scoped admin (no department_id of their own) may
     *    freely re-assign the user to any department, which is how a
     *    student or teacher gets moved into "Data Science", "ITE", etc.
     */
    public function updateUser(Request $request, $id)
    {
        $actor = Auth::user();
        $isDepartmentAdmin = $actor->isDepartmentAdmin();

        $user = User::where('role', '!=', 'super_admin')
            ->where('user_id', '!=', Auth::id())
            ->inDepartments($this->scopedDepartmentIds())
            ->findOrFail($id);

        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user->full_name = $validated['full_name'];
        $user->email = $validated['email'];

        $oldDepartmentId = $user->department_id;
        $newDepartmentId = $isDepartmentAdmin
            ? $actor->department_id
            : ($validated['department_id'] ?? null);

        $user->department_id = $newDepartmentId;
        $user->save();

        // Keep the department_teacher roster pivot in sync so a teacher
        // moved into "ITE" immediately shows up on ITE's teacher roster
        // too, without wiping out any other departments they also teach in.
        if ($user->role === 'teacher' && $newDepartmentId && $newDepartmentId != $oldDepartmentId) {
            $user->departments()->syncWithoutDetaching([$newDepartmentId]);
        }

        $this->logSecurityEvent(Auth::id(), 'updated', 'User Directory', 'Updated profile/department assignment for ' . $user->full_name);

        return redirect()->route('admin.users')->with('success', $user->full_name . ' updated successfully.');
    }

    /**
     * Override administrative entry passkey signoffs safely.
     */
    public function forceResetPassword(Request $request, $id)
    {
        $request->validate(['password' => 'required|string|min:8|confirmed']);
        $user = User::where('role', '!=', 'super_admin')
            ->where('user_id', '!=', Auth::id())
            ->inDepartments($this->scopedDepartmentIds())
            ->findOrFail($id);
        
        $user->password_hash = Hash::make($request->input('password'));
        $user->save();

        $this->logSecurityEvent(Auth::id(), 'created', 'Security Override', 'Forcefully overrode entry passkey signatures for ' . $user->full_name . '.');
        return redirect()->route('admin.users')->with('success', 'Password reset successfully completed for ' . $user->full_name);
    }

    /**
     * Toggle availability node conditions for structural security profiles.
     */
    public function toggleUserStatus($id)
    {
        $user = User::where('role', '!=', 'super_admin')
            ->where('user_id', '!=', Auth::id())
            ->inDepartments($this->scopedDepartmentIds())
            ->findOrFail($id);

        // A 'pending' account is a self-registered teacher awaiting
        // approval (see AuthController::register). The same button an
        // Admin uses to activate/suspend everyone else doubles as the
        // approval action here: pending -> active. From there it behaves
        // like any other account (active <-> suspended toggle below).
        if ($user->status === 'pending') {
            $user->status = 'active';
            $actionName = 'Approved';
        } else {
            $user->status = ($user->status === 'active' || !$user->status) ? 'suspended' : 'active';
            $actionName = $user->status === 'suspended' ? 'Suspended' : 'Activated';
        }

        $user->save();

        $this->logSecurityEvent(Auth::id(), 'comments', 'Status Control', $actionName . ' account for ' . $user->full_name . '.');

        return redirect()->route('admin.users')->with('success', 'Account status updated.');
    }

    /**
     * Permanently drop target user profiles from system indexes.
     */
    public function destroyUser($id)
    {
        $user = User::where('role', '!=', 'super_admin')
            ->where('user_id', '!=', Auth::id())
            ->inDepartments($this->scopedDepartmentIds())
            ->findOrFail($id);
        $user->delete();

        $this->logSecurityEvent(Auth::id(), 'completed', 'Destruction Shield', 'Permanently deleted user account: ' . $user->full_name . '.');
        return redirect()->route('admin.users')->with('success', 'User profile removed securely.');
    }

    /**
     * Stream user workspace configuration models directly to CSV.
     */
    public function exportUsersCsv(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Phnom_Penh');
        $fileName = 'examsystem_users_' . $currentTime->format('Ymd_His') . '.csv';
        $query = User::query()
            ->where('user_id', '!=', Auth::id())
            ->where('role', '!=', 'super_admin')
            ->inDepartments($this->scopedDepartmentIds());

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User ID', 'Full Name', 'Email Address', 'Account Role', 'Last Activity Check-In']);

            $query->chunk(200, function ($users) use ($file) {
                foreach ($users as $user) {
                    $activityTime = $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->timezone('Asia/Phnom_Penh')->format('n/j/Y G:i') : 'No logins recorded';
                    fputcsv($file, [$user->user_id, $user->full_name, $user->email, strtoupper($user->role), $activityTime]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display and manage the Security Audit Center for the signed-in
     * admin's own department(s). A department admin only ever sees
     * login/activity events for students and teachers who belong to
     * their department — a super_admin (or a legacy admin with no
     * department_id) sees every event system-wide.
     */
    public function securityLogWorkspace(Request $request)
    {
        $deptIds = $this->scopedDepartmentIds();

        $totalUsers = User::inDepartments($deptIds)->count();
        $activeExams = Exam::inDepartments($deptIds)->where('status', 'published')->where('start_time', '<=', now())->where('end_time', '>=', now())->count();
        $cpuUsage = $this->getSystemLoadPercentage();
        $activeFilter = $request->input('filter', 'all');

        $actor = Auth::user();
        $isDepartmentAdmin = $actor->isDepartmentAdmin();
        $departmentName = $isDepartmentAdmin ? optional($actor->department)->name : null;

        return view('admin.security', compact('totalUsers', 'activeExams', 'cpuUsage', 'activeFilter', 'isDepartmentAdmin', 'departmentName'));
    }

    /**
     * Live security telemetry — scoped the same way as the page above.
     * A department admin's audit trail is filtered down to only the
     * students/teachers who belong to their department(s); everyone
     * else's activity never appears in their feed.
     */
    public function getSecurityTelemetryApi(Request $request)
    {
        $deptIds = $this->scopedDepartmentIds();

        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.user_id')
            ->select('audit_logs.*', 'users.full_name', 'users.email', 'users.role', 'users.status');

        if (!empty($deptIds)) {
            $query->whereIn('users.department_id', $deptIds);
        }

        $filter = $request->input('filter', 'all');
        if (in_array($filter, ['created', 'uploaded', 'comments', 'completed'])) {
            $query->where('audit_logs.action', '=', $filter);
        }

        $rawLogs = $query->orderBy('audit_logs.created_at', 'desc')->take(15)->get();

        $timelineEvents = $rawLogs->map(function ($log) {
            $payload = json_decode($log->payload, true) ?? [];
            $words = explode(' ', $log->full_name ?? 'System Core');
            $initials = '';
            foreach ($words as $w) { $initials .= $w[0] ?? ''; }
            $initials = strtoupper(substr($initials, 0, 2));

            return [
                'id'          => $log->id,
                'author'      => $log->full_name ?? 'System Core',
                'initials'    => $initials,
                'email'       => $log->email ?? 'system_core@examsystem.com',
                'role'        => strtoupper($log->role ?? 'SYSTEM'),
                'status'      => $log->status ?? 'active',
                'action_type' => $log->action, 
                'target_item' => $payload['target_title'] ?? 'System Asset Instance',
                'description' => $payload['summary'] ?? 'Automated system background process logged.',
                'time_span'   => \Carbon\Carbon::parse($log->created_at)->diffForHumans(),
            ];
        });

        $totalUsers = User::inDepartments($deptIds)->count();
        $activeExams = Exam::inDepartments($deptIds)->where('status', 'published')->where('start_time', '<=', now())->where('end_time', '>=', now())->count();
        $cpuUsage = $this->getSystemLoadPercentage();

        return response()->json([
            'totalUsers'  => $totalUsers,
            'activeExams' => $activeExams,
            'cpuUsage'    => $cpuUsage,
            'events'      => $timelineEvents
        ]);
    }

    public function supportTicketWorkspace(Request $request)
    {
        $totalUsers = User::count();
        $cpuUsage = $this->getSystemLoadPercentage();
        $activeFilter = $request->input('filter', 'all');
        $activeExams = DB::table('support_tickets')->where('status', '!=', 'resolved')->count();

        return view('admin.support', compact('totalUsers', 'activeExams', 'cpuUsage', 'activeFilter'));
    }

    public function getSupportTicketTelemetryApi(Request $request)
    {
        $activeFilter = $request->input('filter', 'all');
        $totalUsers = User::count();
        $cpuUsage = $this->getSystemLoadPercentage();

        $query = DB::table('support_tickets')->orderBy('created_at', 'desc');

        if ($activeFilter === 'pending') {
            $query->where('status', '=', 'pending');
        } elseif ($activeFilter === 'in_progress') {
            $query->where('status', '=', 'in_progress');
        } elseif ($activeFilter === 'resolved') {
            $query->where('status', '=', 'resolved');
        } else {
            $query->where('status', '!=', 'resolved');
        }

        $tickets = $query->get()->map(function ($ticket) {
            return [
                'ticket_id'      => $ticket->ticket_id,
                'ticket_no'      => $ticket->ticket_no,
                'reporter_name'  => $ticket->reporter_name,
                'reporter_email' => $ticket->reporter_email,
                'user_type'      => $ticket->user_type,
                'issue_category' => $ticket->issue_category,
                'priority'       => $ticket->priority,
                'status'         => $ticket->status,
                'description'    => $ticket->description,
                'screenshot'     => $ticket->screenshot,
                'admin_comment'  => $ticket->admin_comment,
                'time_span'      => \Carbon\Carbon::parse($ticket->created_at)->diffForHumans()
            ];
        });

        $activeExams = DB::table('support_tickets')->where('status', '!=', 'resolved')->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'activeExams' => $activeExams,
            'cpuUsage' => $cpuUsage,
            'tickets' => $tickets
        ]);
    }

    public function reviewTicketForm($id)
    {
        $ticket = DB::table('support_tickets')->where('ticket_id', $id)->first();
        if (!$ticket) { return redirect()->route('admin.support')->with('error', 'Ticket record not found.'); }

        $totalUsers = User::count();
        $activeExams = DB::table('support_tickets')->where('status', '!=', 'resolved')->count();
        $cpuUsage = $this->getSystemLoadPercentage();

        return view('admin.resolve_ticket', compact('ticket', 'totalUsers', 'activeExams', 'cpuUsage'));
    }

    public function resolveSupportTicket(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,resolved',
            'admin_comment' => 'required|string|min:5|max:2000'
        ]);

        DB::table('support_tickets')->where('ticket_id', $id)->update([
            'status' => $request->input('status'),
            'admin_comment' => $request->input('admin_comment'),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.support')->with('success', 'Ticket resolution dispatch finalized successfully.');
    }

    /**
     * Keys that make up the live, real-time exam rule set. Kept in one place
     * so the read side (settingsWorkspace / getExamRulesApi) and the write
     * side (updateSystemRules) never drift apart.
     */
    private const EXAM_RULE_KEYS = [
        'proctor_max_switches', 'proctor_warn_threshold', 'block_right_click',
        'force_fullscreen', 'webcam_monitor', 'sync_interval', 'passing_rate',
        'default_time_limit', 'max_attempts',
    ];

    public function settingsWorkspace()
    {
        $totalUsers = User::count();
        $activeExams = Exam::where('status', 'published')->count();
        $cpuUsage = $this->getSystemLoadPercentage();

        $settings = (object) DB::table('system_settings')
            ->whereIn('key', self::EXAM_RULE_KEYS)
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings', compact('totalUsers', 'activeExams', 'cpuUsage', 'settings'));
    }

    public function updateSystemRules(Request $request)
    {
        $validated = $request->validate([
            'proctor_max_switches'   => 'required|integer|min:0|max:20',
            'proctor_warn_threshold' => 'required|integer|min:0|max:20',
            'sync_interval'          => 'required|in:5,10,30',
            'passing_rate'           => 'required|integer|min:0|max:100',
            'default_time_limit'     => 'required|integer|min:5|max:600',
            'max_attempts'           => 'required|integer|min:1|max:10',
        ]);

        $validated['block_right_click'] = $request->has('block_right_click') ? '1' : '0';
        $validated['force_fullscreen']  = $request->has('force_fullscreen') ? '1' : '0';
        $validated['webcam_monitor']    = $request->has('webcam_monitor') ? '1' : '0';

        // ── Enforce the Super Admin's Global Settings > Proctoring Thresholds
        //    as a real floor/ceiling, not just descriptive text on the page. ──
        $global = DB::table('system_settings')
            ->whereIn('key', ['proctor_lockdown', 'max_tab_switches'])
            ->pluck('value', 'key');

        // If the Super Admin has forced fullscreen lockdown globally, the
        // department admin cannot turn it off — silently re-force it rather
        // than reject the whole form, so the rest of the save still succeeds.
        if (($global['proctor_lockdown'] ?? '1') === '1') {
            $validated['force_fullscreen'] = '1';
        }

        // Admin can only match or tighten the global max-tab-switches ceiling,
        // never loosen it — clamp instead of erroring so a well-meaning admin
        // typing a bigger number doesn't lose the rest of their changes.
        $globalMaxSwitches = (int) ($global['max_tab_switches'] ?? 3);
        $validated['proctor_max_switches'] = min((int) $validated['proctor_max_switches'], $globalMaxSwitches);

        DB::transaction(function () use ($validated) {
            foreach ($validated as $key => $value) {
                DB::table('system_settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        });

        // Push this straight into the live security/threat feed so it shows up
        // in the audit stream (which polls every few seconds) immediately.
        $this->logSecurityEvent(
            Auth::id(),
            'comments',
            'Exam Rules',
            "Updated live exam rules — max switches: {$validated['proctor_max_switches']}, warn at: {$validated['proctor_warn_threshold']}, sync every {$validated['sync_interval']}s."
        );

        return redirect()->route('admin.settings')->with('success', 'Global system criteria rules synchronized in real time.');
    }

    /**
     * Lightweight JSON endpoint so any already-open page (student exam room,
     * teacher dashboard, etc.) can pull the current exam rules without a
     * full reload. Polled client-side at the configured sync interval.
     */
    public function getExamRulesApi()
    {
        $raw = DB::table('system_settings')
            ->whereIn('key', array_merge(self::EXAM_RULE_KEYS, ['tab_switch_grace_seconds']))
            ->pluck('value', 'key');

        return response()->json([
            'proctor_max_switches'     => (int) ($raw['proctor_max_switches'] ?? 3),
            'proctor_warn_threshold'   => (int) ($raw['proctor_warn_threshold'] ?? 2),
            'block_right_click'        => ($raw['block_right_click'] ?? '1') === '1',
            'force_fullscreen'         => ($raw['force_fullscreen'] ?? '1') === '1',
            'webcam_monitor'           => ($raw['webcam_monitor'] ?? '0') === '1',
            'sync_interval'            => (int) ($raw['sync_interval'] ?? 10),
            'passing_rate'             => (int) ($raw['passing_rate'] ?? 50),
            'default_time_limit'       => (int) ($raw['default_time_limit'] ?? 60),
            'max_attempts'             => (int) ($raw['max_attempts'] ?? 1),
            // Super Admin global setting — not admin-editable, read-only here.
            'tab_switch_grace_seconds' => (int) ($raw['tab_switch_grace_seconds'] ?? 5),
        ]);
    }

    public function updateAdminProfile(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $request->validate([
            'full_name'          => 'required|string|max:255',
            'avatar_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'telegram_username'  => 'nullable|string|max:255|regex:/^@?[a-zA-Z0-9_]{5,32}$/',
        ], [
            'telegram_username.regex' => 'Enter a valid Telegram username (5-32 letters, numbers, or underscores).',
        ]);

        if (!$user) {
            return redirect()->route('admin.settings')->with('error', 'Could not resolve the signed-in account.');
        }

        $user->full_name = $request->input('full_name');

        // Normalize "@handle" or "handle" down to a bare handle before saving.
        $telegram = $request->input('telegram_username');
        $user->telegram_username = $telegram ? ltrim(trim($telegram), '@') : null;

        if ($request->hasFile('avatar_photo')) {
            // Clean up the old file so avatars don't pile up in storage.
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            try {
                $path = $request->file('avatar_photo')->store('profile_photos', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Admin avatar upload failed', ['error' => $e->getMessage(), 'user_id' => $user->user_id]);

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
                }

                return redirect()->route('admin.settings')->with('error', 'Photo upload failed: ' . $e->getMessage());
            }

            $user->profile_image = $path;

            // Keep user_profiles.avatar_url in sync too — the avatar_url accessor
            // checks profile()->avatar_url FIRST and falls back to profile_image,
            // so a stale value left in user_profiles would silently override this upload.
            if ($user->profile) {
                $user->profile->avatar_url = $path;
                $user->profile->save();
            }
        }

        $user->save();

        $this->logSecurityEvent(Auth::id(), 'comments', 'Admin Profile', 'Updated profile details' . ($request->hasFile('avatar_photo') ? ' and photo' : '') . '.');

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Profile updated successfully.',
                'full_name'  => $user->full_name,
                'avatar_url' => $user->avatar_url,
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'Profile updated successfully.');
    }

    /**
     * Shows the change-password page. Reached two ways:
     *  1) Voluntarily, via the "Change Password" link on the settings page.
     *  2) Forcibly, via ForcePasswordChange middleware, right after a Super
     *     Admin has issued a new temporary password — the account is locked
     *     out of every other admin page until this form is submitted.
     */
    public function passwordWorkspace()
    {
        $user = Auth::user();

        return view('admin.change-password', [
            'forced' => (bool) $user->must_change_password,
        ]);
    }

    /**
     * Handles the actual password change. Always requires the current
     * password (whether that's their own old password, or the temporary
     * one a Super Admin just emailed them) so a hijacked/left-open session
     * can't be used to silently lock the real owner out.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'New password and confirmation do not match.',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password_hash)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->withInput($request->except(['current_password', 'new_password', 'new_password_confirmation']));
        }

        $user->password_hash = Hash::make($request->input('new_password'));
        $user->must_change_password = false;
        $user->save();

        $this->logSecurityEvent(Auth::id(), 'comments', 'Account Security', 'Changed their own password.');

        return redirect()->route('admin.dashboard')->with('success', 'Password updated. Use your new password next time you log in.');
    }

    public function clearDatabaseCache() {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        return redirect()->route('admin.settings')->with('success', 'Application optimization cache pools flushed.');
    }

    public function flushProctoringQueue() {
        DB::table('exam_sessions')->where('status', 'active')->update(['status' => 'suspended', 'updated_at' => now()]);
        return redirect()->route('admin.settings')->with('success', 'Proctor tracking queues reset.');
    }
}