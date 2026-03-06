<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['curricular', 'extra_curricular', 'academic', 'sports', 'cultural', 'seminar', 'workshop']);
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('venue');
            $table->string('organizer');
            $table->enum('target_audience', ['all_students', 'specific_year', 'specific_course', 'faculty', 'all']);
            $table->string('target_audience_specification')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->decimal('registration_fee', 8, 2)->default(0);
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->string('poster_image')->nullable();
            $table->text('requirements')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
