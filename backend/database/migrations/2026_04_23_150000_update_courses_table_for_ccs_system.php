<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('courses', 'academic_year')) {
                $table->string('academic_year')->default('2024-2025')->after('semester');
            }
            if (!Schema::hasColumn('courses', 'instructor')) {
                $table->string('instructor')->nullable()->after('description');
            }
            if (!Schema::hasColumn('courses', 'schedule')) {
                $table->string('schedule')->nullable()->after('instructor');
            }
            if (!Schema::hasColumn('courses', 'room')) {
                $table->string('room')->nullable()->after('schedule');
            }
            if (!Schema::hasColumn('courses', 'max_students')) {
                $table->integer('max_students')->default(30)->after('room');
            }
            if (!Schema::hasColumn('courses', 'current_students')) {
                $table->integer('current_students')->default(0)->after('max_students');
            }
            if (!Schema::hasColumn('courses', 'status')) {
                $table->enum('status', ['active', 'inactive', 'archived'])->default('active')->after('current_students');
            }
            
            // Update existing columns if they exist
            if (Schema::hasColumn('courses', 'level') && !Schema::hasColumn('courses', 'year_level')) {
                $table->renameColumn('level', 'year_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop new columns if they exist
            if (Schema::hasColumn('courses', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
            if (Schema::hasColumn('courses', 'instructor')) {
                $table->dropColumn('instructor');
            }
            if (Schema::hasColumn('courses', 'schedule')) {
                $table->dropColumn('schedule');
            }
            if (Schema::hasColumn('courses', 'room')) {
                $table->dropColumn('room');
            }
            if (Schema::hasColumn('courses', 'max_students')) {
                $table->dropColumn('max_students');
            }
            if (Schema::hasColumn('courses', 'current_students')) {
                $table->dropColumn('current_students');
            }
            if (Schema::hasColumn('courses', 'status')) {
                $table->dropColumn('status');
            }
            
            // Revert existing columns if they exist
            if (Schema::hasColumn('courses', 'year_level') && !Schema::hasColumn('courses', 'level')) {
                $table->renameColumn('year_level', 'level');
            }
        });
    }
};
