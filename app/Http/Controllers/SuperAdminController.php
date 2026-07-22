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
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

        // Audit logs — try-catch in case table doesn't exist yet
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
                    'exam_count'    => DB::table('exams')->where('department_id', $d->id)->count(),
                    'sessions'      => DB::table('exams')->where('department_id', $d->id)->where('status', 'active')->count(),
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
            $examCount = DB::table('exams')->where('department_id', $dept->id)
                ->where('created_at', '>=', now()->subDays($range))->count();

            $flagRate = 0; $trend = 'stable';
            try {
                $examIds = DB::table('exams')->where('department_id', $dept->id)->pluck('exam_id');
                if ($examIds->isEmpty()) {
                    $examIds = DB::table('exams')->where('department_id', $dept->id)->pluck('id');
                }

                if ($examIds->isNotEmpty()) {
                    $total = DB::table('exam_sessions')->whereIn('exam_id', $examIds)->count();
                    $flagged = DB::table('exam_sessions')->whereIn('exam_id', $examIds)->where('is_flagged', true)->count();
                    $flagRate = $total > 0 ? round(($flagged / $total) * 100, 1) : 0;

                    $prevIds = DB::table('exams')->where('department_id', $dept->id)
                        ->where('created_at', '>=', now()->subDays($range * 2))
                        ->where('created_at', '<', now()->subDays($range))->pluck('exam_id');

                    if ($prevIds->isEmpty()) {
                        $prevIds = DB::table('exams')->where('department_id', $dept->id)
                            ->where('created_at', '>=', now()->subDays($range * 2))
                            ->where('created_at', '<', now()->subDays($range))->pluck('id');
                    }

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
        $eventsToday  = 0;
        $criticalCount = 0;
        $uniqueActors = 0;
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

    /** Polling endpoint for audit trail — returns latest logs as JSON. */
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
            ->with(['admins' => fn($q) => $q->select('users.user_id', 'full_name', 'email')])
            ->orderBy('name')->get();

        $unassignedAdmins = User::when($iid, fn($q) => $q->where('institution_id', $iid))
            ->where('role', 'admin')->where('status', 'active')
            ->whereDoesntHave('managedDepartments')->orderBy('full_name')->get();

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
            $dept->admins()->syncWithoutDetaching([$admin->user_id]);
            if (!$admin->department_id) { $admin->department_id = $dept->id; $admin->save(); }
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
            $dept->admins()->detach($admin->user_id);
            if ((int) $admin->department_id === (int) $dept->id) { $admin->department_id = null; $admin->save(); }
            $this->logAction('department.admin.remove', 'DEPARTMENT', $dept->id);
        });

        return redirect()->route('superadmin.departments.index')->with('success', $admin->full_name . ' removed from ' . $dept->name . '.');
    }

    /* ================================================================
     *  BACKUPS / SETTINGS
     * ================================================================ */
    public function backups()
    {
        $lastBackup  = null;
        $storageUsed = $this->getStorageUsedPercent();
        $snapshots   = [];

        try {
            $lastBackup = DB::table('audit_logs')->where('action', 'like', '%backup%')
                ->orderBy('created_at', 'desc')->value('created_at');
            $snapshots = DB::table('audit_logs')->where('action', 'like', '%backup%')
                ->orderBy('created_at', 'desc')->take(10)->get()
                ->map(fn($l) => [
                    'id'         => 'SNAP-' . Carbon::parse($l->created_at)->format('Y-m-d-His'),
                    'created_at' => Carbon::parse($l->created_at)->toDateTimeString(),
                    'size_mb'    => '—',
                    'type'       => str_contains($l->action, 'manual') ? 'manual' : 'automated',
                    'status'     => 'completed',
                ])->toArray();
        } catch (\Exception $e) {}

        return view('superadmin.backups', compact('lastBackup', 'storageUsed', 'snapshots'));
    }

    public function settings()
    {
        $settings = collect();
        try { $settings = DB::table('system_settings')->pluck('value', 'key'); } catch (\Exception $e) {}
        return view('superadmin.global_setting', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $v = $request->validate([
            'site_name'          => 'required|string|max:255',
            'default_lang'       => 'required|string|in:en,km',
            'mail_host'          => 'required|string',
            'mail_password'      => 'required|string',
            'max_tab_switches'   => 'required|integer|min:0|max:10',
            'face_poll_interval' => 'required|string|in:5,15',
        ]);

        $pl = $request->has('proctor_lockdown') ? '1' : '0';

        DB::transaction(function () use ($v, $pl) {
            foreach (array_merge($v, ['proctor_lockdown' => $pl]) as $k => $val) {
                DB::table('system_settings')->where('key', $k)->update(['value' => $val, 'updated_at' => now()]);
            }
            $this->logAction('global.settings.update', 'SYSTEM_CONFIG', '0');
        });

        Artisan::call('config:clear');
        return redirect()->route('superadmin.settings.index')->with('success', 'Global variables written to cache.');
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

            $path = $request->file('avatar')->store('profile_photos', 'public');
            $user->profile_image = $path;
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
        return view('superadmin.user_management', compact('admins'));
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

            if ($u->role === 'teacher' && $did) $u->departments()->syncWithoutDetaching([$did]);
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

        if ($u->status === 'active' && method_exists($u, 'isSoleSuperAdmin') && $u->isSoleSuperAdmin())
            return response()->json(['message' => 'Cannot suspend only Super Admin.'], 422);

        DB::transaction(function () use ($u) {
            $u->status = $u->status === 'active' ? 'suspended' : 'active';
            $u->save();
            $this->logAction('admin.account.toggle_status', 'USER_MANAGEMENT', $u->user_id);
        });

        return response()->json(['status' => 'success']);
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
            $u->role = $v['role']; $u->save();
            $this->logAction('admin.account.role_update', 'USER_MANAGEMENT', $u->user_id);
        });

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

    /**
     * Write one audit-log row using YOUR actual column names.
     */
    private function logAction($action, $modelType, $modelId)
    {
        try {
            DB::table('audit_logs')->insert([
                'user_id'        => auth()->id() ?? null,
                'institution_id' => auth()->user()->institution_id ?? null,
                'action'         => $action,
                'model_type'     => $modelType,
                'model_id'       => $modelId,
                'ip_address'     => request()->ip(),
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // Table may not exist yet — silently fail
        }
    }
}