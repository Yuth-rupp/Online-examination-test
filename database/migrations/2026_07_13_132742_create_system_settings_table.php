<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed baseline core application configuration defaults immediately
        DB::table('system_settings')->insert([
            ['key' => 'site_name', 'value' => 'Online Exam System', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_lang', 'value' => 'en', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'mail_host', 'value' => 'smtp.gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'mail_password', 'value' => 'tavdwpjzkadteibl', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'proctor_lockdown', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_tab_switches', 'value' => '3', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tab_switch_grace_seconds', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};