<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Super Admin: Institution / University directory.
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
            ->get()
            ->map(function ($institution) {
                // If domain is '@', set users_count to total system users
                if ($institution->domain === '@' || empty($institution->domain)) {
                    $institution->users_count = User::count();
                }
                return $institution;
            });

        return view('superadmin.institutions', compact('institutions'));
    }

    /**
     * Onboard a new university by registering its email domain.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:institutions,domain',
        ]);

        // Keep '@' intact for system fallback; otherwise, normalize domain
        $rawDomain = trim($validated['domain']);
        if ($rawDomain === '@') {
            $validated['domain'] = '@';
        } else {
            $validated['domain'] = strtolower(trim($rawDomain, " \t\n\r\0\x0B@"));
        }

        $validated['is_active'] = true;

        $institution = Institution::create($validated);

        \App\Services\AuditLogger::record('institution.create', 'INSTITUTION', $institution->id, [
            'target_title' => 'Institution Directory',
            'summary'      => "Onboarded new university: {$institution->name} ({$institution->domain})",
        ]);

        return redirect()->route('superadmin.institutions.index')
            ->with('success', "{$institution->name} onboarded successfully.");
    }

    /**
     * Edit a university's display name or domain.
     */
    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutions', 'domain')->ignore($institution->id),
            ],
        ]);

        $rawDomain = trim($validated['domain']);
        if ($rawDomain === '@') {
            $validated['domain'] = '@';
        } else {
            $validated['domain'] = strtolower(trim($rawDomain, " \t\n\r\0\x0B@"));
        }

        $institution->update($validated);

        \App\Services\AuditLogger::record('institution.update', 'INSTITUTION', $institution->id, [
            'target_title' => 'Institution Directory',
            'summary'      => "Updated {$institution->name} ({$institution->domain})",
        ]);

        return redirect()->route('superadmin.institutions.index')->with('success', 'Institution updated.');
    }

    /**
     * Flip a university active/inactive.
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
            ->with('success', $institution->name . ($institution->is_active ? ' reactivated.' : ' deactivated.'));
    }

    /**
     * Permanently remove a university from the directory.
     */
    public function destroy(Institution $institution)
    {
        // Protect system fallback institution
        if ($institution->domain === '@') {
            return redirect()->route('superadmin.institutions.index')
                ->with('error', "Can't delete the primary system fallback institution (@).");
        }

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