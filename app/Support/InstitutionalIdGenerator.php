<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class InstitutionalIdGenerator
{
    /**
     * Prefix used per role. Add new roles here if you add new roles
     * to the system.
     */
    protected static array $prefixes = [
        'student'     => 'STU',
        'teacher'     => 'FAC',
        'admin'       => 'ADM',
        'super_admin' => 'SUP',
    ];

    /**
     * Generate a unique institutional ID such as "STU-001-002".
     *
     * Format: {ROLE_PREFIX}-{INSTITUTION, 3 digits}-{SEQUENCE, 3 digits}
     *
     * This is the ONLY place in the app that should decide what the next
     * ID is. It never lets the caller (a registration form, an admin
     * panel, a seeder, etc.) pick the number itself, which is what
     * previously allowed two people to end up with the same ID.
     *
     * How it stays unique even under concurrent registrations:
     *   1. Every (institution, role) pair has exactly one counter row in
     *      `institutional_id_counters`.
     *   2. We open a DB transaction and SELECT ... FOR UPDATE that row,
     *      which makes any other request trying to generate an ID for the
     *      same institution+role WAIT until this transaction commits.
     *      Two people can no longer read the same "next number" at once.
     *   3. We still double check the final candidate against the users
     *      table as a belt-and-braces safety net, and the database-level
     *      unique index on users.institutional_id is the last line of
     *      defense if something upstream is ever bypassed.
     */
    public static function generate(string $role, ?int $institutionId = null): string
    {
        $prefix = static::$prefixes[$role] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $role) ?: 'USR', 0, 3));
        $institutionSegment = $institutionId ?? 1;

        return DB::transaction(function () use ($role, $institutionId, $institutionSegment, $prefix) {
            $counter = DB::table('institutional_id_counters')
                ->where('institution_id', $institutionId)
                ->where('role', $role)
                ->lockForUpdate()
                ->first();

            $next = $counter ? $counter->last_sequence + 1 : 1;

            if ($counter) {
                DB::table('institutional_id_counters')
                    ->where('id', $counter->id)
                    ->update(['last_sequence' => $next, 'updated_at' => now()]);
            } else {
                DB::table('institutional_id_counters')->insert([
                    'institution_id' => $institutionId,
                    'role'           => $role,
                    'last_sequence'  => $next,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $candidate = static::format($prefix, $institutionSegment, $next);

            // Belt-and-braces: if this exact ID somehow already exists
            // (e.g. it was manually assigned before this system existed),
            // keep advancing the counter until we find a free one.
            while (User::where('institutional_id', $candidate)->exists()) {
                $next++;
                DB::table('institutional_id_counters')
                    ->where('institution_id', $institutionId)
                    ->where('role', $role)
                    ->update(['last_sequence' => $next, 'updated_at' => now()]);
                $candidate = static::format($prefix, $institutionSegment, $next);
            }

            return $candidate;
        });
    }

    private static function format(string $prefix, int $institutionSegment, int $sequence): string
    {
        return sprintf('%s-%03d-%03d', $prefix, $institutionSegment, $sequence);
    }
}
