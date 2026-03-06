<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('professor_id')->constrained('professors')->onDelete('cascade');
            $table->string('section');
            $table->string('room');
            $table->enum('room_type', ['lecture', 'lab', 'computer_lab', 'multimedia']);
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('academic_year');
            $table->string('semester');
            $table->integer('max_capacity');
            $table->integer('current_enrollment')->default(0);
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
