<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();
            // Match whatever type your submission primary key uses (unsignedBigInteger or uuid)
            $table->unsignedBigInteger('submission_id'); 
            $table->unsignedBigInteger('question_id');
            $table->text('answer_text')->nullable();
            $table->timestamps();

            // Optional: add indices for faster lookup
            $table->index(['submission_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
    }
};