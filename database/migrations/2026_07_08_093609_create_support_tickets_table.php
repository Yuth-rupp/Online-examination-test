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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id('ticket_id'); // Primary Key used in Admin Resolution Center
            $table->string('ticket_no'); // Random user-facing token identifier (e.g. SUP-4821)
            $table->string('reporter_name');
            $table->string('reporter_email');
            $table->string('user_type')->default('student');
            $table->string('issue_category'); // Stores the subject parameter
            $table->text('description'); // Detailed Operational Information
            $table->string('priority')->default('high');
            $table->string('status')->default('pending'); // pending, in_progress, resolved
            $table->string('screenshot')->nullable(); // Uploaded target attachment file path
            $table->text('admin_comment')->nullable(); // Remediation log comment logs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};