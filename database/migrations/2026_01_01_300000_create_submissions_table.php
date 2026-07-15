<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Initialize the structural table blueprint layout
        Schema::create('submissions', function (Blueprint $table) {
            $table->id(); 
            $table->uuid('exam_id'); // Ensure this matches your Exam table's primary key data type (integer vs UUID)
            
            // Cleaned to match standard 'id' target columns on the users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('session_id'); 
            
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->string('status')->default('pending'); 
            $table->decimal('total_score', 5, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('is_passed')->default(false);
            $table->text('teacher_feedback')->nullable();
            
            // Cleaned to match standard 'id' target column on the users table
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
        });

        // Step 2: Bind the relational database constraint maps safely
        Schema::table('submissions', function (Blueprint $table) {
            // Corrected references to point directly to the true 'id' primary keys
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('exam_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safely isolate and sever the foreign constraints map structure first
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropForeign(['exam_id']);
                $table->dropForeign(['session_id']);
                $table->dropForeign(['user_id']);
                $table->dropForeign(['graded_by']);
            });
        }
        
        // Wipe the physical table out of your database schema engine clean
        Schema::dropIfExists('submissions');
    }
};