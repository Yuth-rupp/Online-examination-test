<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Department;
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
        $totalUsers   = User::where('institution_id', $iid)->count();
        $activeExams  = DB::table('exams')->where('status', 'active')->count();
        $totalExams   = DB::table('exams')->count();
        $liveSessions = $this->countLiveSessions();
        $flagRate     = $this->computeFlagRate();
        $serverLoad   = $this->getServerLoad();
        $usersLW      = User::where('institution_id', $iid)->where('created_at', '<', now()->subDays(7))->count();
        $userGrowth   = $usersLW > 0 ? round((($totalUsers - $usersLW) / $usersLW) * 100, 1) : 0;
        $recentLogs   = AuditLog::with('user')->orderBy('created_at', 'desc')->take(10)->get()
            ->map(fn($l) => ['created_at'=>$l->created_at->diffForHumans(),'operator'=>$l->user?$l->user->full_name:'System','action'=>$l->action,'resource'=>($l->resource_type??'SYSTEM').' [ID: '.($l->resource_id??'0').']','ip'=>$l->ip_address??'—']);
        $lastBackup     = DB::table('audit_logs')->where('action','like','%backup%')->orderBy('created_at','desc')->value('created_at');
        $lastBackupHuman= $lastBackup ? Carbon::parse($lastBackup)->diffForHumans() : 'No backups yet';
        $stuckExams     = DB::table('exams')->where('status','active')->where('updated_at','<',now()->subMinutes(15))->count();
        return view('superadmin.superadmin_dashboard', compact('totalUsers','activeExams','totalExams','liveSessions','flagRate','serverLoad','userGrowth','recentLogs','lastBackupHuman','stuckExams'));
    }
    public function getLiveActivityFeedApi()
    {
        $iid = auth()->user()->institution_id;
        $logs = AuditLog::with('user')->orderBy('created_at','desc')->take(10)->get();
        $feed = $logs->map(fn($l) => ['created_at'=>$l->created_at->diffForHumans(),'operator'=>$l->user?$l->user->full_name:'System','action'=>$l->action,'resource'=>($l->resource_type??'SYSTEM').' [ID: '.($l->resource_id??'0').']','ip'=>$l->ip_address??'—','name'=>$l->action]);
        $totalUsers  = User::where('institution_id',$iid)->count();
        $totalExams  = DB::table('exams')->count();
        $activeExams = DB::table('exams')->where('status','active')->count();
        $liveSessions= $this->countLiveSessions();
        $serverLoad  = $this->getServerLoad();
        $flagRate    = $this->computeFlagRate();
        $usersLW     = User::where('institution_id',$iid)->where('created_at','<',now()->subDays(7))->count();
        $userGrowth  = $usersLW > 0 ? round((($totalUsers - $usersLW) / $usersLW) * 100, 1) : 0;
        $lastBackup  = DB::table('audit_logs')->where('action','like','%backup%')->orderBy('created_at','desc')->value('created_at');
        $stuck       = DB::table('exams')->where('status','active')->where('updated_at','<',now()->subMinutes(15))->get();
        return response()->json(['feed'=>$feed,'totalUsers'=>$totalUsers,'totalExams'=>$totalExams,'activeExams'=>$activeExams,'liveSessions'=>$liveSessions,'serverLoad'=>$serverLoad,'flagRate'=>$flagRate,'userGrowth'=>$userGrowth,'storageUsed'=>$this->getStorageUsedPercent(),'lastBackupHuman'=>$lastBackup?Carbon::parse($lastBackup)->diffForHumans():'No backups yet','stuckExams'=>$stuck->count()]);
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
        $nodeInfo = ['name'=>gethostname()?:'APP-SERVER-01','sessions'=>$liveSessions,'load'=>$serverLoad,'latency'=>$dbLatency,'status'=>$serverLoad<50?'healthy':($serverLoad<80?'warning':'critical')];
        return view('superadmin.monitoring', compact('liveSessions','serverLoad','dbLatency','activeProctors','systemAlerts','nodeInfo'));
    }
    public function monitoringApi()
    {
        $iid = auth()->user()->institution_id;
        $liveSessions   = $this->countLiveSessions();
        $serverLoad     = $this->getServerLoad();
        $dbLatency      = $this->measureDbLatency();
        $activeProctors = $this->getActiveProctors($iid);
        $systemAlerts   = $this->getSystemAlerts();
        $node = ['name'=>gethostname()?:'APP-SERVER-01','sessions'=>$liveSessions,'load'=>$serverLoad,'latency'=>$dbLatency,'status'=>$serverLoad<50?'healthy':($serverLoad<80?'warning':'critical')];
        return response()->json(['metrics'=>['total_sessions'=>$liveSessions,'avg_load'=>$serverLoad,'avg_latency_ms'=>$dbLatency,'nodes_online'=>1,'nodes_total'=>1],'nodes'=>[$node],'teachers'=>$activeProctors,'alerts'=>$systemAlerts]);
    }
    /* ================================================================
     *  EXAMS OVERSIGHT
     * ================================================================ */
    public function exams()
    {
        $iid = auth()->user()->institution_id;
        $totalExams     = DB::table('exams')->count();
        $activeExams    = DB::table('exams')->where('status','active')->count();
        $completedExams = DB::table('exams')->whereIn('status',['completed','ended'])->count();
        $avgFlagRate    = $this->computeFlagRate();
        $allExams = DB::table('exams')->orderBy('created_at','desc')->get()->map(function($e){
            $sc=0;$fc=0;try{$sc=DB::table('exam_sessions')->where('exam_id',$e->id)->count();$fc=DB::table('exam_sessions')->where('exam_id',$e->id)->where('is_flagged',true)->count();}catch(\Exception$x){}
            $e->session_count=$sc;$e->flagged_count=$fc;return $e;
        });
        $departments = DB::table('departments')->where('institution_id',$iid)->get()->map(function($d){
            return (object)['id'=>$d->id,'department'=>$d->name,'exam_count'=>DB::table('exams')->where('department_id',$d->id)->count(),'sessions'=>DB::table('exams')->where('department_id',$d->id)->where('status','active')->count(),'avg_flag_rate'=>0];
        });
        if($departments->isEmpty()) $departments = collect([(object)['department'=>'General Academic','exam_count'=>$totalExams,'sessions'=>$activeExams,'avg_flag_rate'=>0]]);
        $stuckExams = DB::table('exams')->where('status','active')->where('updated_at','<',now()->subMinutes(15))->get();
        return view('superadmin.exams', compact('totalExams','activeExams','completedExams','avgFlagRate','allExams','departments','stuckExams'));
    }
    public function examsApi()
    {
        $totalExams     = DB::table('exams')->count();
        $activeExams    = DB::table('exams')->where('status','active')->count();
        $completedExams = DB::table('exams')->whereIn('status',['completed','ended'])->count();
        $avgFlagRate    = $this->computeFlagRate();
        $stuckExams     = DB::table('exams')->where('status','active')->where('updated_at','<',now()->subMinutes(15))->get();
        return response()->json(['totalExams'=>$totalExams,'activeExams'=>$activeExams,'completedExams'=>$completedExams,'avgFlagRate'=>$avgFlagRate,'stuckCount'=>$stuckExams->count()]);
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
        return view('superadmin.reports_superadmin', compact('range','todayExams','todayUsers','activeNow','avgFlagRate','chartData','departmentStats'));
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
    private function buildChartData(int $range, int $iid): array
    {
        $dates = collect();
        for ($i = $range - 1; $i >= 0; $i--) $dates->push(now()->subDays($i)->toDateString());
        $examCounts = DB::table('exams')->where('created_at','>=',now()->subDays($range))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as cnt')->groupBy('date')->pluck('cnt','date');
        $baseUserCount = User::where('institution_id',$iid)->where('created_at','<',now()->subDays($range))->count();
        $userDaily = DB::table('users')->where('institution_id',$iid)->where('created_at','>=',now()->subDays($range))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as cnt')->groupBy('date')->pluck('cnt','date');
        $flagRates = collect();
        try {
            $dailyFlags = DB::table('exam_sessions')->where('created_at','>=',now()->subDays($range))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN is_flagged = 1 THEN 1 ELSE 0 END) as flagged')
                ->groupBy('date')->get()->keyBy('date');
            foreach ($dates as $d) { $row = $dailyFlags[$d] ?? null; $flagRates[$d] = $row && $row->total > 0 ? round(($row->flagged / $row->total) * 100, 1) : 0; }
        } catch (\Exception $e) { foreach ($dates as $d) $flagRates[$d] = 0; }
        $eL=$eV=$fL=$fV=$uL=$uV=[]; $cum = $baseUserCount;
        if ($range <= 7) {
            foreach ($dates as $d) { $l=Carbon::parse($d)->format('D'); $eL[]=$l;$eV[]=$examCounts[$d]??0;$fL[]=$l;$fV[]=$flagRates[$d]??0;$cum+=$userDaily[$d]??0;$uL[]=$l;$uV[]=$cum; }
        } elseif ($range <= 30) {
            $wn=0;$we=0;$wf=[];
            foreach($dates as $i=>$d){$we+=$examCounts[$d]??0;$wf[]=$flagRates[$d]??0;$cum+=$userDaily[$d]??0;if(($i+1)%7===0||$i===$dates->count()-1){$wn++;$eL[]='Wk '.$wn;$eV[]=$we;$fL[]='Wk '.$wn;$fV[]=count($wf)?round(array_sum($wf)/count($wf),1):0;$uL[]='Wk '.$wn;$uV[]=$cum;$we=0;$wf=[];}}
        } else {
            $grouped=$dates->groupBy(fn($d)=>Carbon::parse($d)->format('M'));
            foreach($grouped as $m=>$md){$me=0;$mf=[];foreach($md as $d){$me+=$examCounts[$d]??0;$mf[]=$flagRates[$d]??0;$cum+=$userDaily[$d]??0;}$eL[]=$m;$eV[]=$me;$fL[]=$m;$fV[]=count($mf)?round(array_sum($mf)/count($mf),1):0;$uL[]=$m;$uV[]=$cum;}
        }
        return ['examLabels'=>$eL,'examValues'=>$eV,'flagLabels'=>$fL,'flagValues'=>$fV,'userLabels'=>$uL,'userValues'=>$uV];
    }
    private function buildDepartmentStats(int $iid, int $range): array
    {
        $departments = DB::table('departments')->where('institution_id',$iid)->get();
        if ($departments->isEmpty()) return [];
        $stats = [];
        foreach ($departments as $dept) {
            $examCount = DB::table('exams')->where('department_id',$dept->id)->where('created_at','>=',now()->subDays($range))->count();
            $flagRate = 0; $trend = 'stable';
            try {
                $examIds = DB::table('exams')->where('department_id',$dept->id)->pluck('id');
                if ($examIds->isNotEmpty()) {
                    $total = DB::table('exam_sessions')->whereIn('exam_id',$examIds)->count();
                    $flagged = DB::table('exam_sessions')->whereIn('exam_id',$examIds)->where('is_flagged',true)->count();
                    $flagRate = $total > 0 ? round(($flagged/$total)*100,1) : 0;
                    $prevIds = DB::table('exams')->where('department_id',$dept->id)->where('created_at','>=',now()->subDays($range*2))->where('created_at','<',now()->subDays($range))->pluck('id');
                    if ($prevIds->isNotEmpty()) { $pT=DB::table('exam_sessions')->whereIn('exam_id',$prevIds)->count(); $pF=DB::table('exam_sessions')->whereIn('exam_id',$prevIds)->where('is_flagged',true)->count(); $pR=$pT>0?round(($pF/$pT)*100,1):0; if($flagRate>$pR+1)$trend='up';elseif($flagRate<$pR-1)$trend='down'; }
                }
            } catch (\Exception $e) {}
            $stats[] = ['name'=>$dept->name,'exam_count'=>$examCount,'flag_rate'=>$flagRate,'trend'=>$trend];
        }
        return $stats;
    }
    /* ================================================================
     *  DEPARTMENTS MANAGEMENT
     * ================================================================ */
    public function departmentsIndex()
    {
        $iid = auth()->user()->institution_id;
        $departments = Department::where('institution_id', $iid)
            ->withCount([
                'users as students_count' => fn($q) => $q->where('role', 'student'),
                'users as teachers_count' => fn($q) => $q->where('role', 'teacher'),
            ])
            ->with(['admins' => fn($q) => $q->select('users.user_id', 'full_name', 'email')])
            ->orderBy('name')
            ->get();
        // Admins not yet assigned to any department
        $unassignedAdmins = User::where('institution_id', $iid)
            ->where('role', 'admin')
            ->where('status', 'active')
            ->whereDoesntHave('managedDepartments')
            ->orderBy('full_name')
            ->get();
        $institutions = DB::table('institutions')->where('id', $iid)->get();
        return view('superadmin.departments', compact('departments', 'unassignedAdmins', 'institutions'));
    }
    public function departmentsStore(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20',
            'description'    => 'nullable|string|max:1000',
        ]);
        // Ensure the institution matches the super admin's institution
        $iid = auth()->user()->institution_id;
        if ((int) $validated['institution_id'] !== $iid) {
            return back()->withErrors(['institution_id' => 'You can only create departments for your own institution.']);
        }
        // Check uniqueness within institution
        if (Department::where('institution_id', $iid)->where('code', $validated['code'])->exists()) {
            return back()->withErrors(['code' => 'A department with this code already exists.']);
        }
        DB::transaction(function () use ($validated) {
            $dept = Department::create([
                'institution_id' => $validated['institution_id'],
                'name'           => $validated['name'],
                'code'           => strtoupper($validated['code']),
                'description'    => $validated['description'] ?? null,
                'is_active'      => true,
            ]);
            $this->logAction('department.create', 'DEPARTMENT', $dept->id);
        });
        return redirect()->route('superadmin.departments.index')->with('success', 'Department "'.$validated['name'].'" created successfully.');
    }
    public function departmentsUpdate(Request $request, $id)
    {
        $iid  = auth()->user()->institution_id;
        $dept = Department::where('institution_id', $iid)->findOrFail($id);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);
        // Code uniqueness (excluding self)
        if (Department::where('institution_id', $iid)->where('code', $validated['code'])->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['code' => 'A department with this code already exists.']);
        }
        DB::transaction(function () use ($dept, $validated, $request) {
            $dept->update([
                'name'        => $validated['name'],
                'code'        => strtoupper($validated['code']),
                'description' => $validated['description'] ?? $dept->description,
                'is_active'   => $request->has('is_active'),
            ]);
            $this->logAction('department.update', 'DEPARTMENT', $dept->id);
        });
        return redirect()->route('superadmin.departments.index')->with('success', 'Department updated.');
    }
    public function departmentsAssignAdmin(Request $request, $id)
    {
        $iid  = auth()->user()->institution_id;
        $dept = Department::where('institution_id', $iid)->findOrFail($id);
        $admin = User::where('institution_id', $iid)->where('role', 'admin')->findOrFail($request->input('user_id'));
        DB::transaction(function () use ($dept, $admin) {
            // Use the admins relationship (many-to-many pivot)
            $dept->admins()->syncWithoutDetaching([$admin->user_id]);
            // Also set department_id on the user for direct reference
            if (!$admin->department_id) {
                $admin->department_id = $dept->id;
                $admin->save();
            }
            $this->logAction('department.admin.assign', 'DEPARTMENT', $dept->id);
        });
        return redirect()->route('superadmin.departments.index')->with('success', $admin->full_name . ' assigned to ' . $dept->name . '.');
    }
    public function departmentsRemoveAdmin($deptId, $userId)
    {
        $iid  = auth()->user()->institution_id;
        $dept = Department::where('institution_id', $iid)->findOrFail($deptId);
        $admin = User::where('institution_id', $iid)->findOrFail($userId);
        DB::transaction(function () use ($dept, $admin) {
            $dept->admins()->detach($admin->user_id);
            // Clear department_id if it matches this department
            if ((int) $admin->department_id === (int) $dept->id) {
                $admin->department_id = null;
                $admin->save();
            }
            $this->logAction('department.admin.remove', 'DEPARTMENT', $dept->id);
        });
        return redirect()->route('superadmin.departments.index')->with('success', $admin->full_name . ' removed from ' . $dept->name . '.');
    }
    /* ================================================================
     *  BACKUPS / AUDIT / SETTINGS
     * ================================================================ */
    public function backups()
    {
        $lastBackup  = DB::table('audit_logs')->where('action','like','%backup%')->orderBy('created_at','desc')->value('created_at');
        $storageUsed = $this->getStorageUsedPercent();
        $snapshots   = DB::table('audit_logs')->where('action','like','%backup%')->orderBy('created_at','desc')->take(10)->get()
            ->map(fn($l)=>['id'=>'SNAP-'.Carbon::parse($l->created_at)->format('Y-m-d-His'),'created_at'=>Carbon::parse($l->created_at)->toDateTimeString(),'size_mb'=>'—','type'=>str_contains($l->action,'manual')?'manual':'automated','status'=>'completed'])->toArray();
        return view('superadmin.backups', compact('lastBackup','storageUsed','snapshots'));
    }
    public function auditLogs() { return view('superadmin.audit_logs'); }
    public function settings()
    {
        $settings = DB::table('system_settings')->pluck('value','key');
        return view('superadmin.global_setting', compact('settings'));
    }
    public function updateSettings(Request $request)
    {
        $v = $request->validate(['site_name'=>'required|string|max:255','default_lang'=>'required|string|in:en,km','mail_host'=>'required|string','mail_password'=>'required|string','max_tab_switches'=>'required|integer|min:0|max:10','face_poll_interval'=>'required|string|in:5,15']);
        $pl = $request->has('proctor_lockdown') ? '1' : '0';
        DB::transaction(function() use ($v,$pl){ foreach(array_merge($v,['proctor_lockdown'=>$pl]) as $k=>$val){ DB::table('system_settings')->where('key',$k)->update(['value'=>$val,'updated_at'=>now()]); } $this->logAction('global.settings.update','SYSTEM_CONFIG','0'); });
        Artisan::call('config:clear');
        return redirect()->route('superadmin.settings.index')->with('success','Global variables written to cache.');
    }
    public function testSmtpConnectionApi(Request $request)
    {
        $addr = $request->input('email', auth()->user()->email);
        try { Mail::raw("SMTP test — ".now()->toDateTimeString(), fn($m)=>$m->to($addr)->subject('SMTP Test')); $this->logAction('settings.smtp.test.success','SYSTEM_CONFIG','0'); return response()->json(['status'=>'success','message'=>"Sent to {$addr}."]); }
        catch(\Throwable $e){ $this->logAction('settings.smtp.test.failure','SYSTEM_CONFIG','0'); return response()->json(['status'=>'error','message'=>'SMTP failed: '.$e->getMessage()],422); }
    }
    /* ================================================================
     *  USER MANAGEMENT
     * ================================================================ */
    public function adminIndex()
    {
        $admins = User::with('department:id,name')
            ->where('institution_id', auth()->user()->institution_id)
            ->orderBy('user_id', 'desc')->get();
        return view('superadmin.user_management', compact('admins'));
    }
    public function adminApiIndex()
    {
        return response()->json(
            User::with('department:id,name')
                ->where('institution_id', auth()->user()->institution_id)
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
        if (!empty($v['department_id']) && !Department::where('id', $v['department_id'])->where('institution_id', $iid)->exists())
            return response()->json(['message' => 'Department not in your institution.'], 422);
        if ($v['role'] === 'super_admin' && User::superAdminExists($iid))
            return response()->json(['message' => 'Super Admin already exists.'], 422);
        $u = DB::transaction(function () use ($v, $iid) {
            $did = $v['role'] === 'super_admin' ? null : ($v['department_id'] ?? null);
            $u = User::create([
                'full_name' => $v['full_name'], 'email' => $v['email'],
                'password_hash' => Hash::make($v['password']), 'role' => $v['role'],
                'status' => 'active', 'institution_id' => $iid, 'department_id' => $did,
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
        $u = User::where('institution_id', auth()->user()->institution_id)->findOrFail($id);
        if ($u->status === 'active' && $u->isSoleSuperAdmin())
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
        $u = User::where('institution_id', $iid)->findOrFail($id);
        if ($v['role'] === 'super_admin' && $u->role !== 'super_admin' && User::superAdminExists($iid))
            return response()->json(['message' => 'Super Admin exists.'], 422);
        if ($v['role'] !== 'super_admin' && $u->isSoleSuperAdmin())
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
            DB::table('exams')->where('id', $id)->update(['status' => 'ended', 'updated_at' => now()]);
            try { DB::table('exam_sessions')->where('exam_id', $id)->where('status', 'in_progress')->update(['status' => 'terminated', 'updated_at' => now()]); } catch (\Exception $e) {}
            $this->logAction('exam.emergency.force_end', 'EXAM_OVERRIDE', $id);
        });
        return response()->json(['status' => 'success', 'message' => 'Exam forcefully closed.']);
    }
    /* ================================================================
     *  REAL-TIME HELPERS
     * ================================================================ */
    private function countLiveSessions(): int { try { return DB::table('exam_sessions')->where('status', 'in_progress')->count(); } catch (\Exception $e) { return DB::table('exams')->where('status', 'active')->count(); } }
    private function computeFlagRate(): float { try { $t = DB::table('exam_sessions')->count(); $f = DB::table('exam_sessions')->where('is_flagged', true)->count(); return $t > 0 ? round(($f / $t) * 100, 1) : 0; } catch (\Exception $e) { return 0; } }
    private function getServerLoad(): int { if (function_exists('sys_getloadavg')) { $l = sys_getloadavg(); $c = 1; if (is_readable('/proc/cpuinfo')) $c = max(1, substr_count(file_get_contents('/proc/cpuinfo'), 'processor')); return min(100, (int) round(($l[0] / $c) * 100)); } return 0; }
    private function getStorageUsedPercent(): float { try { $t = disk_total_space(base_path()); $f = disk_free_space(base_path()); return $t > 0 ? round((($t - $f) / $t) * 100, 1) : 0; } catch (\Exception $e) { return 0; } }
    private function measureDbLatency(): int { $s = microtime(true); DB::select('SELECT 1'); return (int) round((microtime(true) - $s) * 1000); }
    private function getActiveProctors(int $iid): array
    {
        $exams = DB::table('exams')->where('status', 'active')->get();
        $proctors = [];
        foreach ($exams as $e) {
            $tid = $e->user_id ?? $e->teacher_id ?? $e->created_by ?? null;
            if (!$tid) continue;
            $t = User::find($tid);
            if (!$t || $t->institution_id !== $iid) continue;
            $sc = 0; $fc = 0;
            try { $sc = DB::table('exam_sessions')->where('exam_id', $e->id)->where('status', 'in_progress')->count(); $fc = DB::table('exam_sessions')->where('exam_id', $e->id)->where('is_flagged', true)->count(); } catch (\Exception $x) {}
            $sa = $e->started_at ?? $e->updated_at ?? $e->created_at;
            $dur = $sa ? Carbon::parse($sa)->diffForHumans(null, true) : '—';
            $st = 'idle'; if ($sc > 0 && $fc > 3) $st = 'flagging'; elseif ($sc > 0) $st = 'active';
            $proctors[] = ['name' => $t->full_name, 'role' => ucfirst($t->role), 'exam' => $e->title ?? $e->name ?? 'Exam #'.$e->id, 'students' => $sc, 'flags' => $fc, 'duration' => $dur, 'status' => $st];
        }
        return $proctors;
    }
    private function getSystemAlerts(): array
    {
        $alerts = []; $load = $this->getServerLoad();
        if ($load >= 85) $alerts[] = ['severity' => 'critical', 'title' => 'Server Overload', 'message' => "CPU at {$load}%.", 'time' => 'Just now'];
        elseif ($load >= 65) $alerts[] = ['severity' => 'warning', 'title' => 'Server Load Elevated', 'message' => "CPU at {$load}%.", 'time' => 'Just now'];
        $stuck = DB::table('exams')->where('status', 'active')->where('updated_at', '<', now()->subMinutes(15))->count();
        if ($stuck > 0) $alerts[] = ['severity' => 'warning', 'title' => 'Stuck Exams', 'message' => "{$stuck} exam(s) 15+ min without updates.", 'time' => 'Just now'];
        $lat = $this->measureDbLatency();
        if ($lat > 500) $alerts[] = ['severity' => 'critical', 'title' => 'High DB Latency', 'message' => "Round-trip {$lat}ms.", 'time' => 'Just now'];
        elseif ($lat > 200) $alerts[] = ['severity' => 'warning', 'title' => 'Elevated DB Latency', 'message' => "Round-trip {$lat}ms.", 'time' => 'Just now'];
        $recent = AuditLog::whereIn('action', ['exam.emergency.force_end', 'admin.account.toggle_status', 'global.settings.update'])->where('created_at', '>', now()->subHours(1))->orderBy('created_at', 'desc')->take(3)->get();
        foreach ($recent as $l) $alerts[] = ['severity' => 'info', 'title' => ucwords(str_replace(['.', '_'], ' ', $l->action)), 'message' => "On {$l->resource_type} [ID: {$l->resource_id}]", 'time' => $l->created_at->diffForHumans()];
        return $alerts;
    }
    private function logAction($action, $type, $id)
    {
        DB::table('audit_logs')->insert(['user_id' => auth()->id() ?? null, 'action' => $action, 'resource_type' => $type, 'resource_id' => $id, 'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(), 'created_at' => now()]);
    }
}
