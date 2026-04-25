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
        // No-op: unique index is already created in create_course_enrollments migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op because this migration no longer creates an index.
    }
};
