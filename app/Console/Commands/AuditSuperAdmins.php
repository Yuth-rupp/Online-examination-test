<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time cleanup tool for the "anyone could register as super_admin"
 * bug. Run this after deploying the fix to find out how many super_admin
 * accounts currently exist, and (optionally) demote all but one down to
 * a safe role so the system is back to a single source of authority.
 *
 * Usage:
 *   php artisan superadmin:audit
 *       Lists every super_admin account. Makes no changes.
 *
 *   php artisan superadmin:audit --keep=owner@university.edu
 *       Demotes every OTHER super_admin account to 'admin' (with no
 *       department assigned — you'll want to assign one manually
 *       afterward), keeping only the given email as super_admin.
 *
 *   php artisan superadmin:audit --keep=owner@university.edu --demote-to=student
 *       Same as above, but demotes the extras to 'student' instead.
 */
class AuditSuperAdmins extends Command
{
    protected $signature = 'superadmin:audit
                            {--keep= : Email of the super_admin account to keep. If omitted, this is a dry-run listing only.}
                            {--demote-to=admin : Role to assign to extra super_admin accounts (admin or student).}';

    protected $description = 'List all super_admin accounts and optionally demote all but one, to fix duplicate super_admin registrations.';

    public function handle(): int
    {
        $superAdmins = User::where('role', 'super_admin')->orderBy('created_at')->get();

        if ($superAdmins->isEmpty()) {
            $this->warn('No super_admin accounts exist at all. You need to create exactly one — see the seeder note below.');
            $this->line('');
            $this->line('  php artisan tinker');
            $this->line('  >>> App\\Models\\User::create([...\'role\' => \'super_admin\', \'department_id\' => null]);');
            return self::SUCCESS;
        }

        $this->info("Found {$superAdmins->count()} super_admin account(s):");
        $this->table(
            ['user_id', 'full_name', 'email', 'created_at'],
            $superAdmins->map(fn ($u) => [$u->user_id, $u->full_name, $u->email, $u->created_at])
        );

        if ($superAdmins->count() <= 1) {
            $this->info('Nothing to fix — the system already has 0 or 1 super_admin.');
            return self::SUCCESS;
        }

        $keepEmail = $this->option('keep');

        if (!$keepEmail) {
            $this->warn('Multiple super_admin accounts found. This is the exact bug you\'re trying to fix.');
            $this->line('Re-run with --keep=<email> to demote all the others, e.g.:');
            $this->line("  php artisan superadmin:audit --keep={$superAdmins->first()->email}");
            return self::SUCCESS;
        }

        $keeper = $superAdmins->firstWhere('email', $keepEmail);

        if (!$keeper) {
            $this->error("No super_admin account found with email [{$keepEmail}]. Nothing changed.");
            return self::FAILURE;
        }

        $demoteTo = $this->option('demote-to');
        if (!in_array($demoteTo, ['admin', 'student'], true)) {
            $this->error('--demote-to must be either "admin" or "student".');
            return self::FAILURE;
        }

        $extras = $superAdmins->where('email', '!=', $keepEmail);

        if (!$this->confirm("This will demote {$extras->count()} account(s) to '{$demoteTo}', keeping only [{$keepEmail}] as super_admin. Continue?")) {
            $this->info('Cancelled — no changes made.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($extras, $demoteTo) {
            foreach ($extras as $user) {
                $user->role = $demoteTo;
                $user->department_id = null; // admin/teacher/student without a department is intentional — assign one from the Super Admin dashboard afterward.
                $user->save();
                $this->line("Demoted {$user->email} -> {$demoteTo}");
            }
        });

        $this->info('Done. Exactly one super_admin remains: ' . $keepEmail);
        return self::SUCCESS;
    }
}
