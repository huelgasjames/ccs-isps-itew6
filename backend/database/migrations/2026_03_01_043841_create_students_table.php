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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('student_id', 20)->unique()->notNull();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('first_name', 100)->notNull();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->notNull();
            $table->string('email', 255)->unique()->notNull();
            $table->string('phone', 50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();
            
            // Academic Standing
            $table->integer('current_year')->default(1);
            $table->enum('current_semester', ['first', 'second'])->default('first');
            $table->decimal('current_gpa', 3, 2)->default(0.00);
            $table->integer('total_units')->default(0);
            $table->enum('standing', ['excellent', 'good', 'average', 'probation'])->default('good');
            $table->string('advisor', 255)->nullable();
            
            // Legacy fields for compatibility
            $table->string('blood_type', 5)->nullable();
            $table->boolean('disability_status')->default(false);
            $table->string('disability_name', 255)->nullable();
            $table->boolean('scholar')->default(false);
            $table->boolean('working_student')->default(false);
            $table->string('contact_number', 50)->nullable();
            $table->string('section', 50)->nullable();
            $table->enum('year_level', ['1st', '2nd', '3rd', '4th'])->nullable();
            $table->string('college', 100)->default('CCS');
            $table->enum('program', ['BSIT', 'BSCS', 'BSIS', 'BSA', 'BSE'])->nullable();
            $table->string('curriculum', 50)->nullable();
            $table->enum('academic_status', ['Regular', 'Probation', 'Suspended', 'Graduated'])->default('Regular');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
