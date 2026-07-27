<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Global Settings "Face Detection Poll Interval" control was replaced
     * with "Tab-Switch Detection Grace Period". This renames the already-seeded
     * row (rather than losing whatever value was saved on production) so
     * existing deployments keep their configured value instead of silently
     * resetting to the default.
     */
    public function up(): void
    {
        $existing = DB::table('system_settings')->where('key', 'face_poll_interval')->first();

        if ($existing) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'tab_switch_grace_seconds'],
                ['value' => $existing->value, 'updated_at' => now()]
            );
            DB::table('system_settings')->where('key', 'face_poll_interval')->delete();
        } else {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'tab_switch_grace_seconds'],
                ['value' => '5', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $existing = DB::table('system_settings')->where('key', 'tab_switch_grace_seconds')->first();

        if ($existing) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'face_poll_interval'],
                ['value' => $existing->value, 'updated_at' => now()]
            );
            DB::table('system_settings')->where('key', 'tab_switch_grace_seconds')->delete();
        }
    }
};
