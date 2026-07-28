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
     * List every department (super_admin only).
     */
    public function index()
    {
        $iid = auth()->user()->institution_id ?? null;

        $departments = Department::when($iid, fn($q) => $q->where('institution_id', $iid))
            ->withCount([
                'users as students_count' => fn($q) => $q->where('role', 'student'),
                'teachers as teachers_count'
            ])
            ->with(['admins' => fn($q) => $q->select('users.user_id', 'full_name', 'email', 'department_id')])
            ->orderBy('name')
            ->get();

        $institutions = Institution::orderBy('name')->get(['id', 'name']);

        // Admins who don't manage any department yet
        $unassignedAdmins = User::when($iid, fn($q) => $q->where('institution_id', $iid))
            ->where('role', 'admin')
            ->where('status', 'active')
            ->whereNull('department_id')
            ->orderBy('full_name')
            ->get(['users.user_id', 'full_name', 'email']);

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

        $department = Department::create([
            'institution_id' => $validated['institution_id'],
            'name'           => $validated['name'],
            'code'           => strtoupper($validated['code']),
            'description'    => $validated['description'] ?? null,
            'is_active'      => true,
        ]);

        try {
            \App\Services\AuditLogger::record('department.create', 'DEPARTMENT', $department->id, null, $validated['institution_id']);
        } catch (\Exception $e) {}

        return redirect()->route('superadmin.departments.index')->with('success', 'Department "' . $department->name . '" created.');
    }

    /**
     * Rename/edit a department. Super admin only.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');
        $department->update($validated);

        try {
            \App\Services\AuditLogger::record('department.update', 'DEPARTMENT', $department->id, null, $department->institution_id);
        } catch (\Exception $e) {}

        return redirect()->route('superadmin.departments.index')->with('success', 'Department updated.');
    }

    /**
     * Put an existing admin in charge of a department.
     */
    public function assignAdmin(Request $request, Department $department)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);

        $admin = User::where('role', 'admin')->findOrFail($validated['user_id']);
        $admin->department_id = $department->id;
        $admin->save();

        try {
            \App\Services\AuditLogger::record('department.admin.assign', 'DEPARTMENT', $department->id, null, $department->institution_id);
        } catch (\Exception $e) {}

        return redirect()->route('superadmin.departments.index')->with('success', $admin->full_name . ' put in charge of ' . $department->name . '.');
    }

    /**
     * Remove an admin from their department.
     */
    public function removeAdmin(Department $department, $userId)
    {
        $admin = User::where('role', 'admin')->findOrFail($userId);

        if ((int)$admin->department_id === (int)$department->id) {
            $admin->department_id = null;
            $admin->save();
        }

        try {
            \App\Services\AuditLogger::record('department.admin.remove', 'DEPARTMENT', $department->id, null, $department->institution_id);
        } catch (\Exception $e) {}

        return redirect()->route('superadmin.departments.index')->with('success', 'Department admin removed.');
    }

    /* ===================== DEPARTMENT ADMIN: teacher roster ===================== */

    /**
     * Show the teachers currently teaching in this department.
     */
    public function teachers(Department $department)
    {
        $this->authorizeDepartment($department);

        $teachers = $department->teachers()->orderBy('full_name')->get();

        $view = Auth::user()->role === 'super_admin'
            ? 'superadmin.department_teachers'
            : 'admin.department_teachers';

        return view($view, compact('department', 'teachers'));
    }

    /**
     * Search teachers across the institution to pull into department.
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
     * Add an existing teacher to this department.
     */
    public function assignTeacher(Request $request, Department $department)
    {
        $this->authorizeDepartment($department);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);

        $teacher = User::where('role', 'teacher')->findOrFail($validated['user_id']);
        $department->teachers()->syncWithoutDetaching([$teacher->user_id]);

        try {
            \App\Services\AuditLogger::record('department.teacher.assign', 'DEPARTMENT', $department->id, null, $department->institution_id);
        } catch (\Exception $e) {}

        $route = Auth::user()->role === 'super_admin' ? 'superadmin.departments.teachers' : 'admin.departments.teachers';

        return redirect()->route($route, $department)->with('success', 'Teacher added to this department.');
    }

    /**
     * Remove a teacher from this department's teaching roster.
     */
    public function removeTeacher(Department $department, $userId)
    {
        $this->authorizeDepartment($department);

        $department->teachers()->detach($userId);

        $route = Auth::user()->role === 'super_admin' ? 'superadmin.departments.teachers' : 'admin.departments.teachers';

        return redirect()->route($route, $department)->with('success', 'Teacher removed from this department.');
    }
}