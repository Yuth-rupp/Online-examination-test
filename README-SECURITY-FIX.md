# Super Admin / Admin privilege-escalation fix

## What was broken
The public `/register` page let ANYONE choose "Admin" or "Super Admin" from
a dropdown, and the server-side validation (`AuthController::register`)
accepted `admin` and `super_admin` as valid roles with no restriction at
all. That means any visitor to your site could self-register as full
Super Admin with root access to every department.

## Files changed (drop these into your project at the same paths)
- app/Http/Controllers/Api/AuthController.php
    -> Public self-registration now only allows `student` or `teacher`.
- app/Models/User.php
    -> Added `User::superAdminExists()` and `$user->isSoleSuperAdmin()` helpers.
- app/Http/Controllers/SuperAdminController.php
    -> adminStore(): refuses to create a 2nd super_admin.
    -> adminChangeRole(): refuses to promote a 2nd super_admin, and refuses
       to demote the only remaining super_admin.
    -> adminToggleStatus(): refuses to suspend the only remaining super_admin
       (that would lock everyone out, since only a super_admin can un-suspend).
- resources/views/auth/register.blade.php
    -> Removed the Admin / Super Admin options from the public registration
       role dropdown.
- app/Console/Commands/AuditSuperAdmins.php (NEW)
    -> One-time cleanup tool. Your database likely already has multiple
       super_admin accounts from before this fix. Run:

       php artisan superadmin:audit
         (lists all super_admin accounts, dry run)

       php artisan superadmin:audit --keep=the-real-owner@yourschool.edu
         (demotes every OTHER super_admin account to 'admin')

## How the model works now
- Public /register  -> student or teacher only.
- Admin accounts     -> created ONLY by the (one) super_admin, from
                         /super-admin/admins, each scoped to exactly one
                         department (department_id).
- Super Admin        -> exactly one per deployment, not department-scoped,
                         sees/manages everything. Created once (seeder /
                         tinker / the audit command above), never through
                         a public form, and the system now actively blocks
                         creating a second one or removing the last one.

## Still recommended
- Rotate the Gmail app password in your .env (it was exposed when you
  uploaded the project) — Google Account > Security > App Passwords.
- Consider adding an audit-log alert / email to yourself whenever a role
  is changed to super_admin or admin, so you notice immediately if it
  ever happens again.
