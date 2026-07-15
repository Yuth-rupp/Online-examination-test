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
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'difficulty')) {
                $table->string('difficulty')->nullable()->after('content');
            }
            // FIXED: Removed ->after('marks') so it doesn't crash if 'marks' doesn't exist
            if (!Schema::hasColumn('questions', 'points')) {
                $table->integer('points')->nullable(); 
            }
            if (!Schema::hasColumn('questions', 'correct_option')) {
                $table->string('correct_option')->nullable()->after('content');
            }
            if (!Schema::hasColumn('questions', 'option_a')) {
                $table->text('option_a')->nullable();
            }
            if (!Schema::hasColumn('questions', 'option_b')) {
                $table->text('option_b')->nullable();
            }
            if (!Schema::hasColumn('questions', 'option_c')) {
                $table->text('option_c')->nullable();
            }
            if (!Schema::hasColumn('questions', 'option_d')) {
                $table->text('option_d')->nullable();
            }
            if (!Schema::hasColumn('questions', 'tags')) {
                $table->string('tags')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                Schema::hasColumn('questions', 'difficulty') ? 'difficulty' : null,
                Schema::hasColumn('questions', 'points') ? 'points' : null,
                Schema::hasColumn('questions', 'correct_option') ? 'correct_option' : null,
                Schema::hasColumn('questions', 'option_a') ? 'option_a' : null,
                Schema::hasColumn('questions', 'option_b') ? 'option_b' : null,
                Schema::hasColumn('questions', 'option_c') ? 'option_c' : null,
                Schema::hasColumn('questions', 'option_d') ? 'option_d' : null,
                Schema::hasColumn('questions', 'tags') ? 'tags' : null,
            ]);

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};