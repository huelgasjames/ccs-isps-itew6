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
        Schema::create('student_academic_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('school_name', 255)->notNull();
            $table->string('degree', 255)->notNull();
            $table->string('major', 255)->nullable();
            $table->date('start_date')->notNull();
            $table->date('end_date')->notNull();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->json('honors')->nullable();
            $table->enum('status', ['completed', 'ongoing', 'transferred'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_history');
    }
};
