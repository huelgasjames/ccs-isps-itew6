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
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['academic', 'disciplinary', 'attendance', 'conduct'])->notNull();
            $table->text('description')->notNull();
            $table->date('date')->notNull();
            $table->enum('severity', ['minor', 'moderate', 'major', 'severe'])->notNull();
            $table->enum('status', ['pending', 'resolved', 'appealed'])->default('pending');
            $table->string('consequence', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_violations');
    }
};
