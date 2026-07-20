<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Why this migration exists:
     * The create_users_table migration already declares
     * `institutional_id` as unique. That works perfectly on a brand new
     * database. But if that migration file was edited to add the
     * unique() rule AFTER it had already been run once on the live
     * (production) database, Laravel will never re-run it -- so the live
     * table can still be missing the unique index even though the
     * migration file looks correct. This migration is a safety net that:
     *   1. Renames any institutional_id values that are already
     *      duplicated, so the unique index can be created without error.
     *   2. Adds the unique index if it isn't already there.
     * It is safe to run this even if the index already exists.
     */
    public function up(): void
    {
        $this->deduplicateExistingIds();

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('institutional_id', 'users_institutional_id_unique');
            });
        } catch (\Throwable $e) {
            // Index already exists (this migration ran before, or the
            // original migration's unique() already applied) -- nothing
            // to do.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_institutional_id_unique');
            });
        } catch (\Throwable $e) {
            // Nothing to drop.
        }
    }

    /**
     * Find institutional_id values shared by more than one user and keep
     * only the earliest user's ID untouched. Every later duplicate gets a
     * "-DUPn" suffix appended so it becomes unique, is easy to spot in the
     * admin panel, and can be manually corrected to a fresh ID afterward.
     */
    private function deduplicateExistingIds(): void
    {
        $duplicateIds = DB::table('users')
            ->select('institutional_id')
            ->whereNotNull('institutional_id')
            ->groupBy('institutional_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('institutional_id');

        foreach ($duplicateIds as $duplicateId) {
            $rows = DB::table('users')
                ->where('institutional_id', $duplicateId)
                ->orderBy('user_id')
                ->get(['user_id']);

            // Skip the first (oldest) row -- it keeps the original ID.
            foreach ($rows->slice(1) as $index => $row) {
                DB::table('users')
                    ->where('user_id', $row->user_id)
                    ->update([
                        'institutional_id' => $duplicateId . '-DUP' . ($index + 1),
                    ]);
            }
        }
    }
};
