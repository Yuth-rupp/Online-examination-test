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
        Schema::table('submissions', function (Blueprint $table) {
            // Checks if none of your dynamic fallbacks exist before provisioning a fallback column
            if (!Schema::hasColumn('submissions', 'score') && 
                !Schema::hasColumn('submissions', 'marks') && 
                !Schema::hasColumn('submissions', 'total_score') && 
                !Schema::hasColumn('submissions', 'points')) {
                
                $table->decimal('score', 5, 2)->default(0.00)->after('exam_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'score')) {
                $table->dropColumn('score');
            }
        });
    }
};