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
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->string('professor_unique_id', 20)->unique()->notNull();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name', 100)->notNull();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->notNull();
            $table->integer('age')->notNull();
            $table->date('birthday')->notNull();
            $table->string('blood_type', 5)->nullable();
            $table->boolean('disability_status')->default(false);
            $table->enum('gender', ['Male', 'Female', 'Other'])->notNull();
            $table->string('email', 255)->notNull();
            $table->string('contact_number', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('educational_attainment', 255)->nullable();
            $table->text('experience')->nullable();
            $table->text('courses_handled')->nullable();
            $table->string('role', 100)->nullable();
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract'])->nullable();
            $table->string('department', 100)->default('CCS');
            $table->string('organization', 100)->nullable();
            $table->date('application_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professors');
    }
};
