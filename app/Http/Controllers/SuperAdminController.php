<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Exam;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminPasswordReset;
use Carbon\Carbon;
use App\Jobs\CreateBackupJob;
use App\Jobs\RestoreDatabaseJob;

class SuperAdminController extends Controller
{
    /* ================================================================
     *  DASHBOARD
     * ================================================================ */
    public function dashboard()
    {
        $iid = auth()->user()->institution_id;
        $totalUsers   = User::when($iid, fn($q) => $q->where('institution_id', $iid))->count();
        $activeExams  = DB::table('exams')->where('status', 'active')->count();
        $totalExams   = DB::table('exams')->count();
        $liveSessions = $this->countLiveSessions();
        $flagRate     = $this->computeFlagRate();
        $serverLoad   = $this->getServerLoad();
        $usersLW      = User::when($iid, fn($q) => $q->where('institution_id', $iid))
                            ->where('created_at', '<', now()->subDays(7))->count();
        $userGrowth   = $usersLW > 0 ? round((($totalUsers - $usersLW) / $usersLW) * 100, 1) : 0;

        $recentLogs = collect();
        try {
            $recentLogs = AuditLog::with('user')->orderBy('created_at', 'desc')->take(10)->get()
                ->map(fn($l) => [
                    'created_at' => $l->created_at ? $l->created_at->diffForHumans() : '—',
                    'operator'   => $l->user ? $l->user->full_name : 'System',
                    'action'     => $l->action,
                    'resource'   => ($l->model_type ?? 'SYSTEM') . ' [ID: ' . ($l->model_id ?? '0') . ']',
                    'ip'         => $l->ip_address ?? '—',
                ]);
        } catch (\Exception $e) {
            $recentLogs = collect();
        }

        $lastBackupHuman = 'No backups yet';
        try {
            $lastBackup = DB::table('audit_logs')->where('action', 'like', '%backup%')
                ->orderBy('created_at', 'desc')->value('created_at');
            if ($lastBackup) $lastBackupHuman = Carbon::parse($lastBackup)->diffForHumans();
        } catch (\Exception $e) {}

        $stuckExams = DB::table('exams')->where('status', 'active')
            ->where('updated_at', '<', now()->subMinutes(15))->count();

        return view('superadmin.superadmin_dashboard', compact(
            'totalUsers', 'activeExams', 'totalExams', 'liveSessions', 'flagRate',
            'serverLoad', 'userGrowth', 'recentLogs', 'lastBackupHuman', 'stuckExams'
        ));
    }

    public function getLiveActivityFeedApi()
    {
        $iid = auth()->user()->institution_id;
        $feed = collect();
        try {
            $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->take(10)->get();
            $feed = $logs->map(fn($l) => [
                'created_at' => $l->created_at ? $l->created_at->diffForHumans() : '—',
                'operator'   => $l->user ? $l->user->full_name : 'System',
                'action'     => $l->action,
                'resource'   => ($l->model_type ?? 'SYSTEM') . ' [ID: ' . ($l->model_id ?? '0') . ']',
                'ip'         => $l->ip_address ?? '—',
                'name'       => $l->action,
            ]);
        } catch (\Exception $e) {}

        $totalUsers  = User::when($iid, fn($q) => $q->where('institution_id', $iid))->count();
        $totalExams  = DB::table('exams')->count();
        $activeExams = DB::table('exams')->where('status', 'active')->count();
        $liveSessions = $this->countLiveSessions();
        $serverLoad  = $this->getServerLoad();
        $flagRate    = $this->computeFlagRate();
        $usersLW     = User::when($iid, fn($q) => $q->where('institution_id', $iid))
                           ->where('created_at', '<', now()->subDays(7))->count();
        $userGrowth  = $usersLW > 0 ? round((($totalUsers - $usersLW) / $usersLW) * 100, 1) : 0;

        $lastBackupHuman = 'No backups yet';
        try {
            $lb = DB::table('audit_logs')->where('action', 'like', '%backup%')
                ->orderBy('created_at', 'desc')->value('created_at');
            if ($lb) $lastBackupHuman = Carbon::parse($lb)->diffForHumans();
        } catch (\Exception $e) {}

        $stuckCount = DB::table('exams')->where('status', 'active')
            ->where('updated_at', '<', now()->subMinutes(15))->count();

        return response()->json([
            'feed'            => $feed,
            'totalUsers'      => $totalUsers,
            'totalExams'      => $totalExams,
            'activeExams'     => $activeExams,
            'liveSessions'    => $liveSessions,
            'serverLoad'      => $serverLoad,
            'flagRate'        => $flagRate,
            'userGrowth'      => $userGrowth,
            'storageUsed'     => $this->getStorageUsedPercent(),
            'lastBackupHuman' => $lastBackupHuman,
            'stuckExams'      => $stuckCount,
        ]);
    }

    /* ================================================================
     *  MONITORING
     * ================================================================ */
    public function monitoring()
    {
        $iid = auth()->user()->institution_id;
        $liveSessions   = $this->countLiveSessions();
        $serverLoad     = $this->getServerLoad();
        $dbLatency      = $this->measureDbLatency();
        $activeProctors = $this->getActiveProctors($iid);
        $systemAlerts   = $this->getSystemAlerts();

        $nodeInfo = [
            'name'     => gethostname() ?: 'APP-SERVER-01',
            'sessions' => $liveSessions,
            'load'     => $serverLoad,
            'latency'  => $dbLatency,
            'status'   => $serverLoad < 50 ? 'healthy' : ($serverLoad < 80 ? 'warning' : 'critical'),
        ];

        return view('superadmin.monitoring', compact(
            'liveSessions', 'serverLoad', 'dbLatency', 'activeProctors', 'systemAlerts', 'nodeInfo'
        ));
    }

    public function monitoringApi()
    {
        $iid = auth()->user()->institution_id;
        $liveSessions   = $this->countLiveSessions();
        $serverLoad     = $this->getServerLoad();
        $dbLatency      = $this->measureDbLatency();
        $activeProctors = $this->getActiveProctors($iid);
        $systemAlerts   = $this->getSystemAlerts();

        $node = [
            'name'     => gethostname() ?: 'APP-SERVER-01',
            'sessions' => $liveSessions,
            'load'     => $serverLoad,
            'latency'  => $dbLatency,
            'status'   => $serverLoad < 50 ? 'healthy' : ($serverLoad < 80 ? 'warning' : 'critical'),
        ];

        return response()->json([
            'metrics' => [
                'total_sessions' => $liveSessions,
                'avg_load'       => $serverLoad,
                'avg_latency_ms' => $dbLatency,
                'nodes_online'   => 1,
                'nodes_total'    => 1,
            ],
            'nodes'    => [$node],
            'teachers' => $activeProctors,
            'alerts'   => $systemAlerts,
        ]);
    }

    /* ================================================================
     *  EXAMS OVERSIGHT
     * ================================================================ */
    public function exams()
    {
        $iid = auth()->user()->institution_id;
        $totalExams     = DB::table('exams')->count();
        $activeExams    = DB::table('exams')->where('status', 'active')->count();
        $completedExams = DB::table('exams')->whereIn('status', ['completed', 'ended'])->count();
        $avgFlagRate    = $this->computeFlagRate();

        $allExams = DB::table('exams')->orderBy('created_at', 'desc')->get()->map(function ($e) {
            $examId = $e->exam_id ?? $e->id ?? null;
            $sc = 0; $fc = 0;
            try {
                if ($examId) {
                    $sc = DB::table('exam_sessions')->where('exam_id', $examId)->count();
                    $fc = DB::table('exam_sessions')->where('exam_id', $examId)->where('is_flagged', true)->count();
                }
            } catch (\Exception $x) {}
            $e->exam_id = $examId;
            $e->session_count = $sc;
            $e->flagged_count = $fc;
            return $e;
        });

        $departments = DB::table('departments')
            ->when($iid, fn($q) => $q->where('institution_id', $iid))
            ->get()->map(function ($d) {
                return (object) [
                    'id'            => $d->id,
                    'department'    => $d->name,
                    'exam_count'    => DB::table('exams')
                        ->join('courses', 'exams.course_id', '=', 'courses.id')
                        ->where('courses.department_id', $d->id)->count(),
                    'sessions'      => DB::table('exams')
                        ->join('courses', 'exams.course_id', '=', 'courses.id')
                        ->where('courses.department_id', $d->id)
                        ->where('exams.status', 'active')->count(),
                    'avg_flag_rate' => 0,
                ];
            });

        if ($departments->isEmpty()) {
            $departments = collect([(object) [
                'department' => 'General Academic', 'exam_count' => $totalExams,
                'sessions' => $activeExams, 'avg_flag_rate' => 0,
            ]]);
        }

        $stuckExams = DB::table('exams')->where('status', 'active')
            ->where('updated_at', '<', now()->subMinutes(15))->get();

        return view('superadmin.exams', compact(
            'totalExams', 'activeExams', 'completedExams', 'avgFlagRate', 'allExams', 'departments', 'stuckExams'
        ));
    }

    public function examsApi()
    {
        $totalExams     = DB::table('exams')->count();
        $activeExams    = DB::table('exams')->where('status', 'active')->count();
        $completedExams = DB::table('exams')->whereIn('status', ['completed', 'ended'])->count();
        $avgFlagRate    = $this->computeFlagRate();
        $stuckCount     = DB::table('exams')->where('status', 'active')
            ->where('updated_at', '<', now()->subMinutes(15))->count();

        return response()->json([
            'totalExams'     => $totalExams,
            'activeExams'    => $activeExams,
            'completedExams' => $completedExams,
            'avgFlagRate'    => $avgFlagRate,
            'stuckCount'     => $stuckCount,
        ]);
    }

    /* ================================================================
     *  REPORTS & ANALYTICS
     * ================================================================ */
    public function reports()
    {
        $iid   = auth()->user()->institution_id;
        $range = (int) request()->query('range', 7);

        $todayExams  = DB::table('exams')->whereDate('created_at', now()->toDateString())->count();
        $todayUsers  = DB::table('users')->whereDate('created_at', now()->toDateString())->count();
        $activeNow   = $this->countLiveSessions();
        $avgFlagRate = $this->computeFlagRate();

        $chartData       = $this->buildChartData($range, $iid);
        $departmentStats = $this->buildDepartmentStats($iid, $range);

        return view('superadmin.reports_superadmin', compact(
            'range', 'todayExams', 'todayUsers', 'activeNow', 'avgFlagRate', 'chartData', 'departmentStats'
        ));
    }

    public function reportsLiveApi()
    {
        return response()->json([
            'today_exams'   => DB::table('exams')->whereDate('created_at', now()->toDateString())->count(),
            'today_users'   => DB::table('users')->whereDate('created_at', now()->toDateString())->count(),
            'active_now'    => $this->countLiveSessions(),
            'avg_flag_rate' => $this->computeFlagRate(),
        ]);
    }

    public function reportsChartApi(Request $request)
    {
        $iid   = auth()->user()->institution_id;
        $range = (int) $request->query('range', 7);

        return response()->json([
            'chartData'       => $this->buildChartData($range, $iid),
            'departmentStats' => $this->buildDepartmentStats($iid, $range),
        ]);
    }

    private function buildChartData(int $range, $iid): array
    {
        $dates = collect();
        for ($i = $range - 1; $i >= 0; $i--) $dates->push(now()->subDays($i)->toDateString());

        $examCounts = DB::table('exams')->where('created_at', '>=', now()->subDays($range))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as cnt')->groupBy('date')->pluck('cnt', 'date');

        $baseUserCount = User::when($iid, fn($q) => $q->where('institution_id', $iid))
            ->where('created_at', '<', now()->subDays($range))->count();

        $userDaily = DB::table('users')
            ->when($iid, fn($q) => $q->where('institution_id', $iid))
            ->where('created_at', '>=', now()->subDays($range))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as cnt')->groupBy('date')->pluck('cnt', 'date');

        $flagRates = collect();
        try {
            $dailyFlags = DB::table('exam_sessions')->where('created_at', '>=', now()->subDays($range))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN is_flagged = 1 THEN 1 ELSE 0 END) as flagged')
                ->groupBy('date')->get()->keyBy('date');

            foreach ($dates as $d) {
                $row = $dailyFlags[$d] ?? null;
                $flagRates[$d] = $row && $row->total > 0 ? round(($row->flagged / $row->total) * 100, 1) : 0;
            }
        } catch (\Exception $e) {
            foreach ($dates as $d) $flagRates[$d] = 0;
        }

        $eL = $eV = $fL = $fV = $uL = $uV = [];
        $cum = $baseUserCount;

        if ($range <= 7) {
            foreach ($dates as $d) {
                $l = Carbon::parse($d)->format('D');
                $eL[] = $l; $eV[] = $examCounts[$d] ?? 0;
                $fL[] = $l; $fV[] = $flagRates[$d] ?? 0;
                $cum += $userDaily[$d] ?? 0;
                $uL[] = $l; $uV[] = $cum;
            }
        } elseif ($range <= 30) {
            $wn = 0; $we = 0; $wf = [];
            foreach ($dates as $i => $d) {
                $we += $examCounts[$d] ?? 0;
                $wf[] = $flagRates[$d] ?? 0;
                $cum += $userDaily[$d] ?? 0;
                if (($i + 1) % 7 === 0 || $i === $dates->count() - 1) {
                    $wn++;
                    $eL[] = 'Wk ' . $wn; $eV[] = $we;
                    $fL[] = 'Wk ' . $wn; $fV[] = count($wf) ? round(array_sum($wf) / count($wf), 1) : 0;
                    $uL[] = 'Wk ' . $wn; $uV[] = $cum;
                    $we = 0; $wf = [];
                }
            }
        } else {
            $grouped = $dates->groupBy(fn($d) => Carbon::parse($d)->format('M'));
            foreach ($grouped as $m => $md) {
                $me = 0; $mf = [];
                foreach ($md as $d) {
                    $me += $examCounts[$d] ?? 0;
                    $mf[] = $flagRates[$d] ?? 0;
                    $cum += $userDaily[$d] ?? 0;
                }
                $eL[] = $m; $eV[] = $me;
                $fL[] = $m; $fV[] = count($mf) ? round(array_sum($mf) / count($mf), 1) : 0;
                $uL[] = $m; $uV[] = $cum;
            }
        }

        return [
            'examLabels' => $eL, 'examValues' => $eV,
            'flagLabels' => $fL, 'flagValues' => $fV,
            'userLabels' => $uL, 'userValues' => $uV,
        ];
    }

    private function buildDepartmentStats($iid, int $range): array
    {
        $departments = DB::table('departments')
            ->when($iid, fn($q) => $q->where('institution_id', $iid))->get();

        if ($departments->isEmpty()) return [];

        $stats = [];
        foreach ($departments as $dept) {
            $examCount = DB::table('exams')
                ->join('courses', 'exams.course_id', '=', 'courses.id')
                ->where('courses.department_id', $dept->id)
                ->where('exams.created_at', '>=', now()->subDays($range))->count();

            $flagRate = 0; $trend = 'stable';
            try {
                $examIds = DB::table('exams')
                    ->join('courses', 'exams.course_id', '=', 'courses.id')
                    ->where('courses.department_id', $dept->id)
                    ->pluck('exams.exam_id');

                if ($examIds->isNotEmpty()) {
                    $total = DB::table('exam_sessions')->whereIn('exam_id', $examIds)->count();
                    $flagged = DB::table('exam_sessions')->whereIn('exam_id', $examIds)->where('is_flagged', true)->count();
                    $flagRate = $total > 0 ? round(($flagged / $total) * 100, 1) : 0;

                    $prevIds = DB::table('exams')
                        ->join('courses', 'exams.course_id', '=', 'courses.id')
                        ->where('courses.department_id', $dept->id)
                        ->where('exams.created_at', '>=', now()->subDays($range * 2))
                        ->where('exams.created_at', '<', now()->subDays($range))
                        ->pluck('exams.exam_id');

                    if ($prevIds->isNotEmpty()) {
                        $pT = DB::table('exam_sessions')->whereIn('exam_id', $prevIds)->count();
                        $pF = DB::table('exam_sessions')->whereIn('exam_id', $prevIds)->where('is_flagged', true)->count();
                        $pR = $pT > 0 ? round(($pF / $pT) * 100, 1) : 0;

                        if ($flagRate > $pR + 1) $trend = 'up';
                        elseif ($flagRate < $pR - 1) $trend = 'down';
                    }
                }
            } catch (\Exception $e) {}

            $stats[] = ['name' => $dept->name, 'exam_count' => $examCount, 'flag_rate' => $flagRate, 'trend' => $trend];
        }

        return $stats;
    }

    /* ================================================================
     *  AUDIT TRAILS — 100% REAL-TIME
     * ================================================================ */
    public function auditLogs()
    {
        $logs = collect();
        $eventsToday   = 0;
        $criticalCount = 0;
        $uniqueActors  = 0;
        $lastEventTime = '—';

        try {
            $logs = AuditLog::with('user')
                ->orderBy('created_at', 'desc')
                ->take(200)
                ->get()
                ->map(function ($l) {
                    return [
                        'id'         => $l->id,
                        'operator'   => $l->user ? $l->user->full_name : 'System',
                        'role'       => $l->user ? $l->user->role : 'system',
                        'action'     => $l->action ?? '—',
                        'resource'   => ($l->model_type ?? 'SYSTEM') . ($l->model_id ? ' [ID: ' . $l->model_id . ']' : ''),
                        'ip'         => $l->ip_address ?? '—',
                        'created_at' => $l->created_at ? $l->created_at->format('Y-m-d H:i:s') : '—',
                    ];
                });

            $today = now()->toDateString();
            $eventsToday = AuditLog::whereDate('created_at', $today)->count();
            $criticalCount = AuditLog::where(function ($q) {
                $q->where('action', 'like', '%force_end%')
                  ->orWhere('action', 'like', '%emergency%')
                  ->orWhere('action', 'like', '%delete%')
                  ->orWhere('action', 'like', '%wipe%');
            })->count();

            $uniqueActors = AuditLog::distinct('user_id')->count('user_id');
            $last = AuditLog::orderBy('created_at', 'desc')->first();
            $lastEventTime = $last && $last->created_at ? $last->created_at->diffForHumans() : '—';
        } catch (\Exception $e) {}

        return view('superadmin.audit_logs', compact(
            'logs', 'eventsToday', 'criticalCount', 'uniqueActors', 'lastEventTime'
        ));
    }

    public function auditLogsApi()
    {
        try {
            $logs = AuditLog::with('user')
                ->orderBy('created_at', 'desc')
                ->take(200)
                ->get()
                ->map(function ($l) {
                    return [
                        'id'         => $l->id,
                        'operator'   => $l->user ? $l->user->full_name : 'System',
                        'role'       => $l->user ? $l->user->role : 'system',
                        'action'     => $l->action ?? '—',
                        'resource'   => ($l->model_type ?? 'SYSTEM') . ($l->model_id ? ' [ID: ' . $l->model_id . ']' : ''),
                        'ip'         => $l->ip_address ?? '—',
                        'created_at' => $l->created_at ? $l->created_at->format('Y-m-d H:i:s') : '—',
                    ];
                });

            $today = now()->toDateString();

            return response()->json([
                'logs'          => $logs,
                'total'         => AuditLog::count(),
                'events_today'  => AuditLog::whereDate('created_at', $today)->count(),
                'critical'      => AuditLog::where(function ($q) {
                    $q->where('action', 'like', '%force_end%')
                      ->orWhere('action', 'like', '%emergency%')
                      ->orWhere('action', 'like', '%delete%')
                      ->orWhere('action', 'like', '%wipe%');
                })->count(),
                'unique_actors' => AuditLog::distinct('user_id')->count('user_id'),
                'last_event'    => optional(AuditLog::orderBy('created_at', 'desc')->first())->created_at
                    ? AuditLog::orderBy('created_at', 'desc')->first()->created_at->diffForHumans() : '—',
            ]);
        } catch (\Exception $e) {
            return response()->json(['logs' => [], 'total' => 0, 'events_today' => 0, 'critical' => 0, 'unique_actors' => 0, 'last_event' => '—']);
        }
    }

    /* ================================================================
     *  DEPARTMENTS MANAGEMENT
     * ================================================================ */
    public function departmentsIndex()
    {
        $iid = auth()->user()->institution_id;
        $departments = Department::when($iid, fn($q) => $q->where('institution_id', $iid))
            ->withCount([
                'users as students_count' => fn($q) => $q->where('role', 'student'),
                'users as teachers_count' => fn($q) => $q->where('role', 'teacher'),
            ])
            ->with(['admins' => fn($q) => $q->select('users.user_id', 'full_name', 'email', 'department_id')])
            ->orderBy('name')->get();

        $unassignedAdmins = User::when($iid, fn($q) => $q->where('institution_id', $iid))
            ->where('role', 'admin')->where('status', 'active')
            ->whereNull('department_id')->orderBy('full_name')->get();

        $institutions = DB::table('institutions')
            ->when($iid, fn($q) => $q->where('id', $iid))->get();

        return view('superadmin.departments', compact('departments', 'unassignedAdmins', 'institutions'));
    }

    public function departmentsStore(Request $request)
    {
        $v = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20',
            'description'    => 'nullable|string|max:1000',
        ]);

        $iid = auth()->user()->institution_id;
        if ($iid && (int) $v['institution_id'] !== $iid)
            return back()->withErrors(['institution_id' => 'You can only create departments for your own institution.']);

        if (Department::where('institution_id', $v['institution_id'])->where('code', $v['code'])->exists())
            return back()->withErrors(['code' => 'A department with this code already exists.']);

        DB::transaction(function () use ($v) {
            $dept = Department::create([
                'institution_id' => $v['institution_id'],
                'name'           => $v['name'],
                'code'           => strtoupper($v['code']),
                'description'    => $v['description'] ?? null,
                'is_active'      => true,
            ]);
            $this->logAction('department.create', 'DEPARTMENT', $dept->id);
        });

        return redirect()->route('superadmin.departments.index')->with('success', 'Department "' . $v['name'] . '" created.');
    }

    public function departmentsUpdate(Request $request, $id)
    {
        $iid  = auth()->user()->institution_id;
        $dept = Department::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($id);
        $v = $request->validate(['name' => 'required|string|max:255', 'code' => 'required|string|max:20', 'description' => 'nullable|string|max:1000']);

        if (Department::where('institution_id', $dept->institution_id)->where('code', $v['code'])->where('id', '!=', $id)->exists())
            return back()->withErrors(['code' => 'A department with this code already exists.']);

        DB::transaction(function () use ($dept, $v, $request) {
            $dept->update(['name' => $v['name'], 'code' => strtoupper($v['code']), 'description' => $v['description'] ?? $dept->description, 'is_active' => $request->has('is_active')]);
            $this->logAction('department.update', 'DEPARTMENT', $dept->id);
        });

        return redirect()->route('superadmin.departments.index')->with('success', 'Department updated.');
    }

    public function departmentsAssignAdmin(Request $request, $id)
    {
        $iid  = auth()->user()->institution_id;
        $dept = Department::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($id);
        $admin = User::when($iid, fn($q) => $q->where('institution_id', $iid))->where('role', 'admin')->findOrFail($request->input('user_id'));

        DB::transaction(function () use ($dept, $admin) {
            $admin->department_id = $dept->id;
            $admin->save();
            $this->logAction('department.admin.assign', 'DEPARTMENT', $dept->id);
        });

        return redirect()->route('superadmin.departments.index')->with('success', $admin->full_name . ' assigned to ' . $dept->name . '.');
    }

    public function departmentsRemoveAdmin($deptId, $userId)
    {
        $iid  = auth()->user()->institution_id;
        $dept = Department::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($deptId);
        $admin = User::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($userId);

        DB::transaction(function () use ($dept, $admin) {
            if ((int) $admin->department_id === (int) $dept->id) { 
                $admin->department_id = null; 
                $admin->save(); 
            }
            $this->logAction('department.admin.remove', 'DEPARTMENT', $dept->id);
        });

        return redirect()->route('superadmin.departments.index')->with('success', $admin->full_name . ' removed from ' . $dept->name . '.');
    }

    /* ================================================================
     *  DATABASE & BACKUP
     * ================================================================ */
    public function backups()
    {
        $lastBackup  = null;
        $storageUsed = $this->getStorageUsedPercent();
        $snapshots   = $this->getSnapshotsFromDisk();

        if (!empty($snapshots)) {
            $lastBackup = $snapshots[0]['created_at'] ?? null;
        } else {
            try {
                $lastBackup = DB::table('audit_logs')
                    ->where('action', 'like', '%backup%')
                    ->orderBy('created_at', 'desc')
                    ->value('created_at');
            } catch (\Exception $e) {}
        }

        return view('superadmin.backups', compact('lastBackup', 'storageUsed', 'snapshots'));
    }

    public function backupApi()
    {
        $snapshots   = $this->getSnapshotsFromDisk();
        $storageUsed = $this->getStorageUsedPercent();

        $lastBackupHuman = 'No backups yet';
        if (!empty($snapshots)) {
            try {
                $lastBackupHuman = Carbon::parse($snapshots[0]['created_at'])->diffForHumans();
            } catch (\Exception $e) {
                $lastBackupHuman = $snapshots[0]['created_at'] ?? 'Unknown';
            }
        }

        return response()->json([
            'snapshots'       => $snapshots,
            'storageUsed'     => $storageUsed,
            'lastBackupHuman' => $lastBackupHuman,
        ]);
    }

    public function triggerBackup()
    {
        $user = auth()->user();

        CreateBackupJob::dispatch(
            $user->full_name ?? $user->name ?? 'Super Admin',
            'manual'
        );

        $this->logAction('backup.manual.triggered', 'DATABASE_BACKUP', '0');

        return response()->json([
            'status'  => 'success',
            'message' => 'Backup job dispatched. You will receive real-time updates via WebSocket.',
        ], 202);
    }

    public function restoreBackup(Request $request, $snapshotId)
    {
        $request->validate([
            'confirm_phrase' => 'required|string',
        ]);

        if ($request->input('confirm_phrase') !== 'RESTORE') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid confirmation phrase.',
            ], 422);
        }

        $filepath = storage_path('app/backups/' . $snapshotId . '.sql');
        if (!file_exists($filepath)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Snapshot file not found on disk.',
            ], 404);
        }

        $user = auth()->user();

        $this->logAction('backup.restore.triggered.CRITICAL', 'DATABASE_RESTORE', $snapshotId);

        RestoreDatabaseJob::dispatch(
            $snapshotId,
            $user->full_name ?? $user->name ?? 'Super Admin'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Restore job dispatched. You will receive real-time updates via WebSocket.',
        ], 202);
    }

    public function deleteBackup($snapshotId)
    {
        $filepath = storage_path('app/backups/' . $snapshotId . '.sql');

        if (!file_exists($filepath)) {
            return response()->json(['status' => 'error', 'message' => 'Snapshot not found.'], 404);
        }

        @unlink($filepath);
        $this->logAction('backup.snapshot.deleted', 'DATABASE_BACKUP', $snapshotId);

        return response()->json(['status' => 'success', 'message' => 'Snapshot deleted.']);
    }

    private function getSnapshotsFromDisk(): array
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            return $this->getSnapshotsFromAuditLogs();
        }

        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'SNAP-*.sql');

        if (empty($files)) {
            return $this->getSnapshotsFromAuditLogs();
        }

        $snapshots = [];
        foreach ($files as $file) {
            $basename   = pathinfo($file, PATHINFO_FILENAME);
            $sizeMb     = round(filesize($file) / 1024 / 1024, 2);

            $dateStr = str_replace('SNAP-', '', $basename);
            $parts   = explode('-', $dateStr);

            if (count($parts) >= 4) {
                $timePart = $parts[3];
                $timeFormatted = substr($timePart, 0, 2) . ':' . substr($timePart, 2, 2) . ':' . substr($timePart, 4, 2);
                $createdAt = "{$parts[0]}-{$parts[1]}-{$parts[2]} {$timeFormatted}";
            } else {
                $createdAt = date('Y-m-d H:i:s', filemtime($file));
            }

            $type = 'automated';
            try {
                $log = DB::table('audit_logs')
                    ->where('model_id', $basename)
                    ->where('action', 'like', '%backup%')
                    ->first();

                if ($log && str_contains($log->action, 'manual')) {
                    $type = 'manual';
                }
            } catch (\Exception $e) {}

            $snapshots[] = [
                'id'         => $basename,
                'created_at' => $createdAt,
                'size_mb'    => $sizeMb,
                'type'       => $type,
                'status'     => 'completed',
                'filename'   => $basename . '.sql',
            ];
        }

        usort($snapshots, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return array_slice($snapshots, 0, 20);
    }

    private function getSnapshotsFromAuditLogs(): array
    {
        try {
            return DB::table('audit_logs')
                ->where('action', 'like', '%backup%')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(fn($l) => [
                    'id'         => 'SNAP-' . Carbon::parse($l->created_at)->format('Y-m-d-His'),
                    'created_at' => Carbon::parse($l->created_at)->toDateTimeString(),
                    'size_mb'    => '—',
                    'type'       => str_contains($l->action, 'manual') ? 'manual' : 'automated',
                    'status'     => 'completed',
                ])
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /* ================================================================
     *  SETTINGS
     * ================================================================ */
    public function settings()
    {
        $settings = collect();
        $configCount = 0;
        $lastUpdated = null;

        try {
            $settings = DB::table('system_settings')->pluck('value', 'key');
            $configCount = $settings->count();
            $lastUpdated = DB::table('system_settings')->max('updated_at');
        } catch (\Exception $e) {}

        $smtpConfigured = !empty($settings['mail_host']) && !empty($settings['mail_password']);
        $lockdownEnforced = ($settings['proctor_lockdown'] ?? '1') === '1';
        $auditRetentionDays = $settings['audit_retention_days'] ?? '90';

        return view('superadmin.global_setting', compact(
            'settings', 'configCount', 'lastUpdated', 'smtpConfigured', 'lockdownEnforced', 'auditRetentionDays'
        ));
    }

    public function updateSettings(Request $request)
    {
        $fields = $request->except(['_token']);

        DB::transaction(function () use ($fields) {
            foreach ($fields as $key => $value) {
                DB::table('system_settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now()]
                );
            }
            $this->logAction('global.settings.update', 'SYSTEM_CONFIG', '0');
        });

        Artisan::call('config:clear');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Settings saved and applied to all departments.',
            ]);
        }

        return redirect()->route('superadmin.settings.index')
            ->with('success', 'Global settings updated — applied to all departments.');
    }

    public function testSmtpConnectionApi(Request $request)
    {
        $addr = $request->input('email', auth()->user()->email);
        try {
            Mail::raw("SMTP test — " . now()->toDateTimeString(), fn($m) => $m->to($addr)->subject('SMTP Test'));
            $this->logAction('settings.smtp.test.success', 'SYSTEM_CONFIG', '0');
            return response()->json(['status' => 'success', 'message' => "Sent to {$addr}."]);
        } catch (\Throwable $e) {
            $this->logAction('settings.smtp.test.failure', 'SYSTEM_CONFIG', '0');
            return response()->json(['status' => 'error', 'message' => 'SMTP failed: ' . $e->getMessage()], 422);
        }
    }

    public function clearDatabaseCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $this->logAction('settings.cache.clear', 'SYSTEM_CONFIG', '0');
            return response()->json(['status' => 'success', 'message' => 'All caches cleared across all departments.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function optimizeDatabaseTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = 'Tables_in_' . config('database.connections.mysql.database');
            foreach ($tables as $table) {
                $tableName = $table->$dbName;
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
            }
            $this->logAction('settings.db.optimize', 'SYSTEM_CONFIG', '0');
            return response()->json(['status' => 'success', 'message' => 'All database tables optimized.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function clearSystemLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file)) { unlink($file); $count++; }
            }
            $this->logAction('settings.logs.clear', 'SYSTEM_CONFIG', '0');
            return response()->json(['status' => 'success', 'message' => "Cleared {$count} log file(s)."]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function flushProctoringQueue()
    {
        try {
            Artisan::call('queue:clear');
            $this->logAction('settings.queue.flush', 'SYSTEM_CONFIG_CRITICAL', '0');
            return response()->json(['status' => 'success', 'message' => 'Queue flushed — all pending jobs cleared.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function purgeSystemAuditLogs()
    {
        try {
            $retentionDays = DB::table('system_settings')
                ->where('key', 'audit_retention_days')
                ->value('value') ?? 90;

            if ($retentionDays == 0) {
                return response()->json(['status' => 'error', 'message' => 'Retention is set to "Forever" — nothing to purge.']);
            }

            $cutoff = now()->subDays((int) $retentionDays);
            $deleted = DB::table('audit_logs')->where('created_at', '<', $cutoff)->delete();
            $this->logAction('settings.audit.purge', 'SYSTEM_CONFIG_CRITICAL', '0');

            return response()->json([
                'status'  => 'success',
                'message' => "Purged {$deleted} audit entries older than {$retentionDays} days.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user() ?? auth()->user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user->full_name = $request->input('full_name');

        if ($request->hasFile('avatar')) {
            if ($user->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_image);
            }

            try {
                $path = $request->file('avatar')->store('profile_photos', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SuperAdmin avatar upload failed', ['error' => $e->getMessage(), 'user_id' => $user->user_id]);

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
                }

                return redirect()->back()->with('error', 'Photo upload failed: ' . $e->getMessage());
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
        $this->logAction('profile.update', 'USER', (string) $user->user_id);

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Profile updated successfully.',
                'full_name'  => $user->full_name,
                'avatar_url' => $user->avatar_url,
            ]);
        }

        return redirect()->route('superadmin.settings.index')->with('success', 'Profile updated successfully.');
    }

    /* ================================================================
     *  USER MANAGEMENT
     * ================================================================ */
    public function adminIndex()
    {
        $iid = auth()->user()->institution_id;

        $admins = User::with('department:id,name')
            ->when($iid, fn($q) => $q->where('institution_id', $iid))
            ->orderBy('user_id', 'desc')->get();

        // Pass departments so the front end can show assignment dropdowns
        $departments = DB::table('departments')
            ->when($iid, fn($q) => $q->where('institution_id', $iid))
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('superadmin.user_management', compact('admins', 'departments'));
    }

    public function adminApiIndex()
    {
        $iid = auth()->user()->institution_id;
        return response()->json(
            User::with('department:id,name')
                ->when($iid, fn($q) => $q->where('institution_id', $iid))
                ->orderBy('user_id', 'desc')->get()
        );
    }

    public function adminStore(Request $request)
    {
        $iid = $request->user()->institution_id;
        $v = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'role'          => 'required|string|in:student,teacher,admin,super_admin',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if (!empty($v['department_id']) && $iid && !Department::where('id', $v['department_id'])->where('institution_id', $iid)->exists())
            return response()->json(['message' => 'Department not in your institution.'], 422);

        if ($v['role'] === 'super_admin' && User::superAdminExists($iid))
            return response()->json(['message' => 'Super Admin already exists.'], 422);

        $u = DB::transaction(function () use ($v, $iid) {
            $did = $v['role'] === 'super_admin' ? null : ($v['department_id'] ?? null);
            $u = User::create([
                'full_name'      => $v['full_name'],
                'email'          => $v['email'],
                'password_hash'  => Hash::make($v['password']),
                'role'           => $v['role'],
                'status'         => 'active',
                'institution_id' => $iid,
                'department_id'  => $did,
            ]);

            if ($u->role === 'teacher' && $did) {
                $u->departments()->syncWithoutDetaching([$did]);
            }

            $this->logAction('admin.account.create', 'USER_MANAGEMENT', $u->user_id);
            return $u;
        });

        return $request->wantsJson()
            ? response()->json(['status' => 'success', 'user' => $u], 201)
            : redirect()->route('superadmin.admins.index')->with('success', 'Account created.');
    }

    public function adminToggleStatus($id)
    {
        if (auth()->id() == $id) return response()->json(['message' => 'Denied.'], 403);

        $iid = auth()->user()->institution_id;
        $u = User::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($id);

        if (method_exists($u, 'isSoleSuperAdmin') && $u->isSoleSuperAdmin())
            return response()->json(['message' => 'Cannot delete the only Super Admin.'], 422);

        DB::transaction(function () use ($u) {
            $this->logAction('admin.account.delete', 'USER_MANAGEMENT', $u->user_id);
            $u->delete();
        });

        return response()->json(['status' => 'success', 'message' => 'Account deleted.']);
    }

    public function adminChangeRole(Request $request, $id)
    {
        if (auth()->id() == $id) return response()->json(['message' => 'Denied.'], 403);

        $v = $request->validate(['role' => 'required|string|in:student,teacher,admin,super_admin']);
        $iid = auth()->user()->institution_id;
        $u = User::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($id);

        if ($v['role'] === 'super_admin' && $u->role !== 'super_admin' && User::superAdminExists($iid))
            return response()->json(['message' => 'Super Admin exists.'], 422);

        if ($v['role'] !== 'super_admin' && method_exists($u, 'isSoleSuperAdmin') && $u->isSoleSuperAdmin())
            return response()->json(['message' => 'Cannot change only Super Admin role.'], 422);

        DB::transaction(function () use ($u, $v) {
            $u->role = $v['role']; 
            $u->save();
            $this->logAction('admin.account.role_update', 'USER_MANAGEMENT', $u->user_id);
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * Change a user's department assignment.
     * Used by the inline dropdown on the User Management page.
     */
    public function adminChangeDepartment(Request $request, $id)
    {
        if (auth()->id() == $id) {
            return response()->json(['message' => 'Cannot change your own department.'], 403);
        }

        $iid = auth()->user()->institution_id;
        $u = User::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($id);

        $deptId = $request->input('department_id');

        if ($deptId) {
            $exists = DB::table('departments')
                ->where('id', $deptId)
                ->when($iid, fn($q) => $q->where('institution_id', $iid))
                ->exists();

            if (!$exists) {
                return response()->json(['message' => 'Department not found in your institution.'], 422);
            }
        }

        DB::transaction(function () use ($u, $deptId) {
            // Update the user's direct department_id column
            $u->department_id = $deptId ?: null;
            $u->save();

            // If user is teacher, sync the department_teacher pivot
            if ($u->role === 'teacher' && method_exists($u, 'departments')) {
                if ($deptId) {
                    $u->departments()->syncWithoutDetaching([$deptId]);
                }
            }

            $this->logAction('admin.user.department.change', 'USER_MANAGEMENT', $u->user_id);
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * Reset a user's (admin/teacher/etc.) password.
     * Generates a new random temporary password, saves it, logs the action,
     * and returns the plaintext password once so the Super Admin can hand it
     * to the account owner. It is never stored or logged in plaintext.
     */
    public function adminResetPassword($id)
    {
        if (auth()->id() == $id) {
            return response()->json(['message' => 'Use your own profile settings to change your password.'], 403);
        }

        $iid = auth()->user()->institution_id;
        $u = User::when($iid, fn($q) => $q->where('institution_id', $iid))->findOrFail($id);

        $newPassword = Str::password(12);

        DB::transaction(function () use ($u, $newPassword) {
            $u->password_hash = Hash::make($newPassword);
            $u->save();
            $this->logAction('admin.account.password_reset', 'USER_MANAGEMENT', $u->user_id);
        });

        try {
            Mail::to($u->email)->send(new AdminPasswordReset($u->full_name, $newPassword));
        } catch (\Throwable $e) {
            // Password was still reset successfully even if the email fails to send;
            // the Super Admin can still copy/share it manually from the modal.
            Log::error('AdminPasswordReset mail failed', ['user_id' => $u->user_id, 'email' => $u->email, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'status'       => 'success',
            'message'      => 'Password reset successfully and emailed to the admin.',
            'new_password' => $newPassword,
        ]);
    }

    /* ================================================================
     *  ADMIN PASSWORD RESET REQUESTS
     * ================================================================ */
    public function passwordRequests()
    {
        $requests = \App\Models\AdminPasswordResetRequest::with('user:user_id,full_name,email,department_id')
            ->orderByRaw("status = 'pending' desc")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superadmin.password_requests', compact('requests'));
    }

    public function resolvePasswordRequest($id)
    {
        $requestRow = \App\Models\AdminPasswordResetRequest::with('user')->findOrFail($id);

        if (!$requestRow->user) {
            return response()->json(['message' => 'That admin account no longer exists.'], 404);
        }

        $newPassword = Str::password(12);

        DB::transaction(function () use ($requestRow, $newPassword) {
            $requestRow->user->password_hash = Hash::make($newPassword);
            $requestRow->user->save();

            $requestRow->status      = 'resolved';
            $requestRow->resolved_by = auth()->id();
            $requestRow->resolved_at = now();
            $requestRow->save();

            $this->logAction('admin.account.password_reset', 'USER_MANAGEMENT', $requestRow->user->user_id);
        });

        try {
            Mail::to($requestRow->user->email)->send(new AdminPasswordReset($requestRow->user->full_name, $newPassword));
        } catch (\Throwable $e) {
            // Password was still reset successfully even if the email fails to send;
            // the Super Admin can still copy/share it manually from the modal.
            Log::error('AdminPasswordReset mail failed', ['user_id' => $requestRow->user->user_id, 'email' => $requestRow->user->email, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'status'       => 'success',
            'message'      => 'Password reset successfully and emailed to the admin.',
            'new_password' => $newPassword,
            'admin_name'   => $requestRow->user->full_name,
            'admin_email'  => $requestRow->user->email,
        ]);
    }

    public function dismissPasswordRequest($id)
    {
        $requestRow = \App\Models\AdminPasswordResetRequest::findOrFail($id);
        $requestRow->update([
            'status'      => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }

    /* ================================================================
     *  EMERGENCY OVERRIDES
     * ================================================================ */
    public function forceEndExam($id)
    {
        DB::transaction(function () use ($id) {
            DB::table('exams')
                ->where('exam_id', $id)
                ->orWhere('id', $id)
                ->update(['status' => 'ended', 'updated_at' => now()]);

            try {
                DB::table('exam_sessions')
                    ->where('exam_id', $id)
                    ->where('status', 'in_progress')
                    ->update(['status' => 'terminated', 'updated_at' => now()]);
            } catch (\Exception $e) {}

            $this->logAction('exam.emergency.force_end', 'EXAM_OVERRIDE', $id);
        });

        return response()->json(['status' => 'success', 'message' => 'Exam forcefully closed.']);
    }

    /* ================================================================
     *  REAL-TIME HELPERS
     * ================================================================ */
    private function countLiveSessions(): int
    {
        try { return DB::table('exam_sessions')->where('status', 'in_progress')->count(); }
        catch (\Exception $e) { return DB::table('exams')->where('status', 'active')->count(); }
    }

    private function computeFlagRate(): float
    {
        try {
            $t = DB::table('exam_sessions')->count();
            $f = DB::table('exam_sessions')->where('is_flagged', true)->count();
            return $t > 0 ? round(($f / $t) * 100, 1) : 0;
        } catch (\Exception $e) { return 0; }
    }

    private function getServerLoad(): int
    {
        if (function_exists('sys_getloadavg')) {
            $l = sys_getloadavg();
            $c = 1;
            if (is_readable('/proc/cpuinfo'))
                $c = max(1, substr_count(file_get_contents('/proc/cpuinfo'), 'processor'));
            return min(100, (int) round(($l[0] / $c) * 100));
        }
        return 0;
    }

    private function getStorageUsedPercent(): float
    {
        try {
            $t = disk_total_space(base_path());
            $f = disk_free_space(base_path());
            return $t > 0 ? round((($t - $f) / $t) * 100, 1) : 0;
        } catch (\Exception $e) { return 0; }
    }

    private function measureDbLatency(): int
    {
        $s = microtime(true);
        DB::select('SELECT 1');
        return (int) round((microtime(true) - $s) * 1000);
    }

    private function getActiveProctors($iid): array
    {
        $exams = DB::table('exams')->where('status', 'active')->get();
        $proctors = [];

        foreach ($exams as $e) {
            $tid = $e->user_id ?? $e->teacher_id ?? $e->created_by ?? null;
            if (!$tid) continue;

            $t = User::find($tid);
            if (!$t) continue;
            if ($iid && $t->institution_id !== $iid) continue;

            $examId = $e->exam_id ?? $e->id ?? null;
            $sc = 0; $fc = 0;

            try {
                if ($examId) {
                    $sc = DB::table('exam_sessions')->where('exam_id', $examId)->where('status', 'in_progress')->count();
                    $fc = DB::table('exam_sessions')->where('exam_id', $examId)->where('is_flagged', true)->count();
                }
            } catch (\Exception $x) {}

            $sa  = $e->started_at ?? $e->updated_at ?? $e->created_at;
            $dur = $sa ? Carbon::parse($sa)->diffForHumans(null, true) : '—';
            $st  = 'idle';

            if ($sc > 0 && $fc > 3) $st = 'flagging';
            elseif ($sc > 0) $st = 'active';

            $proctors[] = [
                'name'     => $t->full_name,
                'role'     => ucfirst($t->role),
                'exam'     => $e->title ?? $e->name ?? 'Exam #' . ($examId ?? '0'),
                'students' => $sc,
                'flags'    => $fc,
                'duration' => $dur,
                'status'   => $st,
            ];
        }

        return $proctors;
    }

    private function getSystemAlerts(): array
    {
        $alerts = [];
        $load = $this->getServerLoad();

        if ($load >= 85) $alerts[] = ['severity' => 'critical', 'title' => 'Server Overload', 'message' => "CPU at {$load}%.", 'time' => 'Just now'];
        elseif ($load >= 65) $alerts[] = ['severity' => 'warning', 'title' => 'Server Load Elevated', 'message' => "CPU at {$load}%.", 'time' => 'Just now'];

        $stuck = DB::table('exams')->where('status', 'active')->where('updated_at', '<', now()->subMinutes(15))->count();
        if ($stuck > 0) $alerts[] = ['severity' => 'warning', 'title' => 'Stuck Exams', 'message' => "{$stuck} exam(s) 15+ min without updates.", 'time' => 'Just now'];

        $lat = $this->measureDbLatency();
        if ($lat > 500) $alerts[] = ['severity' => 'critical', 'title' => 'High DB Latency', 'message' => "Round-trip {$lat}ms.", 'time' => 'Just now'];
        elseif ($lat > 200) $alerts[] = ['severity' => 'warning', 'title' => 'Elevated DB Latency', 'message' => "Round-trip {$lat}ms.", 'time' => 'Just now'];

        try {
            $recent = AuditLog::whereIn('action', ['exam.emergency.force_end', 'admin.account.toggle_status', 'global.settings.update'])
                ->where('created_at', '>', now()->subHours(1))->orderBy('created_at', 'desc')->take(3)->get();

            foreach ($recent as $l) {
                $alerts[] = [
                    'severity' => 'info',
                    'title'    => ucwords(str_replace(['.', '_'], ' ', $l->action)),
                    'message'  => 'On ' . ($l->model_type ?? 'SYSTEM') . ' [ID: ' . ($l->model_id ?? '0') . ']',
                    'time'     => $l->created_at ? $l->created_at->diffForHumans() : '—',
                ];
            }
        } catch (\Exception $e) {}

        return $alerts;
    }

    private function logAction($action, $modelType, $modelId)
    {
        \App\Services\AuditLogger::record($action, $modelType, $modelId);
    }
}