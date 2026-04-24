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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('semester');
            $table->string('academic_year');
            $table->decimal('grade', 4, 2)->nullable();
            $table->string('status')->default('enrolled'); // enrolled, dropped, completed, failed
            $table->date('enrollment_date')->default(now());
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Prevent duplicate enrollments
            $table->unique(['student_id', 'course_id', 'semester', 'academic_year'], 'course_enrollment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
