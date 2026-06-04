<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradingRubricToSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Adds the structural rubric point columns to the table safely
            $table->integer('accuracy_score')->default(0)->after('status');
            $table->integer('depth_score')->default(0)->after('accuracy_score');
            $table->integer('clarity_score')->default(0)->after('depth_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['accuracy_score', 'depth_score', 'clarity_score']);
        });
    }
}