<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $defaults = [
            'proctor_max_switches'   => '3',
            'proctor_warn_threshold' => '2',
            'block_right_click'      => '1',
            'force_fullscreen'       => '1',
            'webcam_monitor'         => '0',
            'sync_interval'          => '10',
            'passing_rate'           => '50',
            'default_time_limit'     => '60',
            'max_attempts'           => '1',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', [
            'proctor_max_switches', 'proctor_warn_threshold', 'block_right_click',
            'force_fullscreen', 'webcam_monitor', 'sync_interval', 'passing_rate',
            'default_time_limit', 'max_attempts',
        ])->delete();
    }
};
