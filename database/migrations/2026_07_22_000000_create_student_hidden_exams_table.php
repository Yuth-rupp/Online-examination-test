<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a student "delete" (dismiss) an exam card from their own
     * My Exams page without touching the underlying exam, submission,
     * or grade data — the teacher and other students are unaffected.
     */
    public function up(): void
    {
        Schema::create('student_hidden_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->uuid('exam_id');
            $table->timestamps();

            $table->unique(['user_id', 'exam_id']);
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_hidden_exams');
    }
};