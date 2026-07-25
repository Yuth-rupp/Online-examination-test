<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a department Admin store their own Telegram handle so students
     * and teachers in their department can reach them directly from the
     * "Forgot Password" screen (self-service reset is disabled for those
     * roles — they must contact their department admin instead).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_username')->nullable()->after('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_username');
        });
    }
};
