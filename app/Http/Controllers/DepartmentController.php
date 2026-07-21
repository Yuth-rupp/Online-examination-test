<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Abort with 403 unless the signed-in user is allowed to manage this
     * specific department: a super_admin can manage any department, a
     * department admin can only manage the one department they belong to.
     */
    private function authorizeDepartment(Department $department): void
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return;
        }

        if ($user->role === 'admin' && $user->department_id === $department->id) {
            return;
        }

        abort(403, 'You do not have permission to manage this department.');
    }

    /* ===================== SUPER ADMIN: department directory ===================== */

    /**
     * List every department (super_admin only — this is the "create the
     * departments and hand each one to an admin" screen).
     */
    public function index()
    {
        $departments = Department::withCount(['students', 'teachers'])
            ->with(['admins:user_id,full_name,email,department_id'])
            ->orderBy('name')
            ->get();

        $institutions = Institution::orderBy('name')->get(['id', 'name']);

        // Admins who don't manage any department yet — candidates to assign.
        $unassignedAdmins = User::where('role', 'admin')->whereNull('department_id')->get(['user_id', 'full_name', 'email']);

        return view('superadmin.departments', compact('departments', 'institutions', 'unassignedAdmins'));
    }

    /**
     * Create a new department. Super admin only.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20',
            'description'    => 'nullable|string|max:1000',
        ]);

        $department = Department::create($validated);

        DB::table('audit_logs')->insert([
            'user_id'    => Auth::id(),
            'action'     => 'created',
            'payload'    => json_encode([
                'target_title' => 'Department Directory',
                'summary'      => 'Created new department: ' . $department->name,
            ]),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        return redirect()->route('superadmin.departments.index')->with('success', 'Department created.');
    }

    /**
     * Rename/edit a department. Super admin only.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $department->update($validated);

        return redirect()->route('superadmin.departments.index')->with('success', 'Department updated.');
    }

    /**
     * Put an existing admin in charge of a department (this is what turns
     * a plain "admin" into a scoped "department admin" — it just sets
     * their users.department_id). Super admin only.
     */
    public function assignAdmin(Request $request, Department $department)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);

        $admin = User::where('role', 'admin')->findOrFail($validated['user_id']);
        $admin->department_id = $department->id;
        $admin->save();

        DB::table('audit_logs')->insert([
            'user_id'    => Auth::id(),
            'action'     => 'comments',
            'payload'    => json_encode([
                'target_title' => 'Department Directory',
                'summary'      => "Put {$admin->full_name} in charge of {$department->name}.",
            ]),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        return redirect()->route('superadmin.departments.index')->with('success', 'Department admin assigned.');
    }

    /**
     * Remove an admin from their department (they become an unscoped/
     * global admin again until reassigned). Super admin only.
     */
    public function removeAdmin(Department $department, $userId)
    {
        $admin = User::where('role', 'admin')->where('department_id', $department->id)->findOrFail($userId);
        $admin->department_id = null;
        $admin->save();

        return redirect()->route('superadmin.departments.index')->with('success', 'Department admin removed.');
    }

    /* ===================== DEPARTMENT ADMIN: teacher roster for their department ===================== */

    /**
     * Show the teachers currently teaching in this department, plus a
     * search box to pull in a teacher who already exists elsewhere in the
     * system (this is the "one teacher, many departments" screen).
     * Available to the department's own admin, or any super_admin.
     */
    public function teachers(Department $department)
    {
        $this->authorizeDepartment($department);

        $teachers = $department->teachers()->orderBy('full_name')->get();

        return view('admin.department_teachers', compact('department', 'teachers'));
    }

    /**
     * Search teachers by name/email across the whole institution, so a
     * department admin can find a teacher who is already teaching
     * somewhere else and add them here too. Read-only — only name/email
     * are exposed, not the teacher's full profile.
     */
    public function searchTeachers(Request $request, Department $department)
    {
        $this->authorizeDepartment($department);

        $search = trim((string) $request->input('q', ''));

        $teachers = User::where('role', 'teacher')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('full_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->whereDoesntHave('departments', function ($q) use ($department) {
                $q->where('departments.id', $department->id);
            })
            ->limit(15)
            ->get(['user_id', 'full_name', 'email']);

        return response()->json($teachers);
    }

    /**
     * Add an existing teacher to this department (attaches the pivot row —
     * does NOT change the teacher's home department_id, and does NOT
     * remove them from any other department they already teach in).
     */
    public function assignTeacher(Request $request, Department $department)
    {
        $this->authorizeDepartment($department);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);

        $teacher = User::where('role', 'teacher')->findOrFail($validated['user_id']);
        $department->teachers()->syncWithoutDetaching([$teacher->user_id]);

        DB::table('audit_logs')->insert([
            'user_id'    => Auth::id(),
            'action'     => 'comments',
            'payload'    => json_encode([
                'target_title' => 'Department Teaching Roster',
                'summary'      => "Added {$teacher->full_name} to teach in {$department->name}.",
            ]),
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        return redirect()->route('admin.departments.teachers', $department)->with('success', 'Teacher added to this department.');
    }

    /**
     * Remove a teacher from this department's teaching roster (they keep
     * teaching in any other department they're still linked to).
     */
    public function removeTeacher(Department $department, $userId)
    {
        $this->authorizeDepartment($department);

        $department->teachers()->detach($userId);

        return redirect()->route('admin.departments.teachers', $department)->with('success', 'Teacher removed from this department.');
    }
}
