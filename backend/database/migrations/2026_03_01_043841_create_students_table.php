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
            $table->string('student_unique_id', 20)->unique()->notNull();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name', 100)->notNull();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->notNull();
            $table->integer('age')->notNull();
            $table->string('blood_type', 5)->nullable();
            $table->boolean('disability_status')->default(false);
            $table->string('disability_name', 255)->nullable();
            $table->boolean('scholar')->default(false);
            $table->boolean('working_student')->default(false);
            $table->string('email', 255)->notNull();
            $table->string('contact_number', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('section', 50)->nullable();
            $table->enum('year_level', ['1st', '2nd', '3rd', '4th'])->nullable();
            $table->string('college', 100)->default('CCS');
            $table->enum('program', ['BSIT', 'BSCS'])->notNull();
            $table->string('curriculum', 50)->nullable();
            $table->enum('academic_status', ['Regular', 'Probation', 'Suspended', 'Graduated'])->default('Regular');
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
