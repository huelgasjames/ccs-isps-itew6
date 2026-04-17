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
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('name', 100)->notNull();
            $table->enum('category', ['technical', 'soft', 'business', 'creative', 'sports', 'language'])->notNull();
            $table->enum('proficiency', ['beginner', 'intermediate', 'advanced', 'expert', 'native'])->notNull();
            $table->text('certifications')->nullable();
            $table->integer('years_experience')->default(0);
            $table->date('last_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skills');
    }
};
