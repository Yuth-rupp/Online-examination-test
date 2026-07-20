<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table holds one row per (institution_id, role) pair and stores the
     * last sequence number that was handed out for that pair. Generating a
     * new institutional ID becomes an atomic "lock row -> increment -> save"
     * operation instead of guessing based on existing users, which is what
     * made it possible for two people to end up with the same ID before.
     */
    public function up(): void
    {
        Schema::create('institutional_id_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->string('role');
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();

            $table->unique(['institution_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutional_id_counters');
    }
};
