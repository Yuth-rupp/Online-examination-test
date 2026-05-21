<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Custom Primary Key matching User.php
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('set null');
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('role')->default('student'); // e.g., admin, teacher, student
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};