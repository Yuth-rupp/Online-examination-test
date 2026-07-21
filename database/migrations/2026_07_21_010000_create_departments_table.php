<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A department is a division inside one institution (e.g. "Data
     * Science", "Bio Engineering", "Mathematics"). Every department admin,
     * teacher, and student is tied to a department through this table.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('name');
            $table->string('code', 20); // short code, e.g. "DS", "BIO", "MATH"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Code only needs to be unique within one institution, not globally.
            $table->unique(['institution_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
