<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admins (unlike students/teachers) don't have a department admin above
     * them to contact — only the Super Admin can reset their password. This
     * table logs each request an admin submits from the "Forgot Password"
     * screen so the Super Admin has a clear, auditable inbox to work from
     * instead of relying on someone messaging them out of band.
     */
    public function up(): void
    {
        Schema::create('admin_password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('email');
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending | resolved
            $table->foreignId('resolved_by')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_password_reset_requests');
    }
};
