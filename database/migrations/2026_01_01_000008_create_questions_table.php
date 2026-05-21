<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('exam_id')->nullable();
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
            $table->foreignId('question_bank_id')->nullable()->constrained('question_banks')->onDelete('set null');
            $table->string('type'); // multiple_choice, essay, true_false
            $table->text('content');
            $table->json('options')->nullable();
            $table->json('correct_answer')->nullable();
            $table->decimal('marks', 5, 2);
            $table->integer('order')->default(0);
            $table->text('explanation')->nullable();
            $table->string('media_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};