<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Super Admin: Institution / University directory.
 *
 * This is what onboards a new school onto the platform. AuthController::register()
 * refuses self-registration unless the student/teacher's email domain matches an
 * `institutions.domain` row with `is_active = true` — until a university is added
 * here, nobody with that email domain can create an account.
 */
class InstitutionController extends Controller
{
    /**
     * List every university/institution currently onboarded, with a quick
     * count of how many users belong to each.
     */
    public function index()
    {
        $institutions = Institution::withCount('users')
            ->orderBy('name')
            ->get();

        return view('superadmin.institutions', compact('institutions'));
    }

    /**
     * Onboard a new university by registering its email domain. From this
     * moment on, anyone with an @that-domain email can self-register as a
     * student or teacher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:institutions,domain',
        ]);

        // Normalize the same way AuthController::register() reads it back
        // (lowercase, no leading "@", no stray whitespace) so a domain
        // typed as "@RUPP.edu.kh" still matches "student@rupp.edu.kh".
        $validated['domain'] = strtolower(trim($validated['domain'], " \t\n\r\0\x0B@"));
        $validated['is_active'] = true;

        $institution = Institution::create($validated);

        \App\Services\AuditLogger::record('institution.create', 'INSTITUTION', $institution->id, [
            'target_title' => 'Institution Directory',
            'summary'      => "Onboarded new university: {$institution->name} ({$institution->domain})",
        ]);

        return redirect()->route('superadmin.institutions.index')
            ->with('success', "{$institution->name} can now self-register with @{$institution->domain} emails.");
    }

    /**
     * Edit a university's display name or domain.
     */
    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:institutions,domain,' . $institution->id,
        ]);

        $validated['domain'] = strtolower(trim($validated['domain'], " \t\n\r\0\x0B@"));

        $institution->update($validated);

        \App\Services\AuditLogger::record('institution.update', 'INSTITUTION', $institution->id, [
            'target_title' => 'Institution Directory',
            'summary'      => "Updated {$institution->name} ({$institution->domain})",
        ]);

        return redirect()->route('superadmin.institutions.index')->with('success', 'Institution updated.');
    }

    /**
     * Flip a university active/inactive. Deactivating it immediately blocks
     * new self-registrations from that domain (existing accounts are
     * untouched — this only guards the registration form).
     */
    public function toggleStatus(Institution $institution)
    {
        $institution->is_active = !$institution->is_active;
        $institution->save();

        \App\Services\AuditLogger::record($institution->is_active ? 'institution.activated' : 'institution.deactivated', 'INSTITUTION', $institution->id, [
            'target_title' => 'Institution Directory',
            'summary'      => ($institution->is_active ? 'Reactivated' : 'Deactivated') . " {$institution->name} ({$institution->domain})",
        ]);

        return redirect()->route('superadmin.institutions.index')
            ->with('success', $institution->name . ($institution->is_active ? ' reactivated.' : ' deactivated — new self-registrations from that domain are now blocked.'));
    }

    /**
     * Permanently remove a university from the directory. Blocked if it
     * still has users or departments attached — those must be reassigned
     * or removed first, otherwise this would silently orphan real data.
     */
    public function destroy(Institution $institution)
    {
        $userCount = \App\Models\User::where('institution_id', $institution->id)->count();
        $deptCount = \App\Models\Department::where('institution_id', $institution->id)->count();

        if ($userCount > 0 || $deptCount > 0) {
            return redirect()->route('superadmin.institutions.index')
                ->with('error', "Can't delete {$institution->name} — it still has {$userCount} user(s) and {$deptCount} department(s) attached. Reassign or remove those first.");
        }

        $name = $institution->name;

        \App\Services\AuditLogger::record('institution.delete', 'INSTITUTION', $institution->id, [
            'target_title' => 'Institution Directory',
            'summary'      => "Deleted institution: {$name}",
        ]);

        $institution->delete();

        return redirect()->route('superadmin.institutions.index')->with('success', "{$name} was permanently deleted.");
    }
}