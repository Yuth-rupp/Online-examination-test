<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the users table structure first
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Custom Primary Key matching User.php
            $table->unsignedBigInteger('institution_id')->nullable(); // Column created without immediate constraint
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('role')->default('student'); 
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Attach the foreign key constraint safely afterward
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('institution_id')
                  ->references('id')
                  ->on('institutions')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign key first to avoid constraint conflicts on rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
        });

        Schema::dropIfExists('users');
    }
};