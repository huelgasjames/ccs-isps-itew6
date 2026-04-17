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
        Schema::create('student_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('name', 255)->notNull();
            $table->enum('type', ['organization', 'sports', 'research', 'volunteer', 'club'])->notNull();
            $table->string('role', 100)->nullable();
            $table->date('start_date')->notNull();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->json('achievements')->nullable();
            $table->enum('level', ['local', 'regional', 'national', 'international'])->default('local');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activities');
    }
};
