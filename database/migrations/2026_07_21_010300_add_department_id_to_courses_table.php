<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A course belongs to exactly one department. This is what lets a
     * department admin see only "their" courses/exams, and it's also how
     * we know which department a given exam counts toward when a teacher
     * teaches in more than one department.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('institution_id')
                  ->constrained('departments')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
