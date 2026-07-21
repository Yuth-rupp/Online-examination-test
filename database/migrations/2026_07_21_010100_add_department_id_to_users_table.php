<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * users.department_id is the user's PRIMARY department:
     *  - for a student: the department they belong to
     *  - for a department admin: the one department they are allowed to manage
     *  - for a teacher: their "home" department (a teacher can ALSO be linked
     *    to extra departments through the department_teacher pivot table —
     *    see the next migration — so one teacher can teach in several
     *    departments at once)
     *  - for super_admin: always left null, because a super admin is not
     *    scoped to any single department
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('institution_id')
                  ->constrained('departments')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
