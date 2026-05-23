<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id(); 
            $table->uuid('exam_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status')->default('active'); 
            $table->dateTime('joined_at')->useCurrent();
            $table->dateTime('left_at')->nullable();
            $table->json('flags')->nullable();
            $table->timestamps();

            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};