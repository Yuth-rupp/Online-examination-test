<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Solves "one teacher teaches Data Science AND Bio Engineering AND
     * Mathematics" — a simple many-to-many link between teachers and
     * departments. A row here means "this teacher is allowed to teach /
     * be assigned courses inside this department", on top of whatever
     * their users.department_id "home" department already is.
     */
    public function up(): void
    {
        Schema::create('department_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['department_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_teacher');
    }
};
