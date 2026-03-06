<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 20)->unique();
            $table->string('course_name');
            $table->text('description')->nullable();
            $table->integer('credits');
            $table->string('department');
            $table->enum('level', ['1st', '2nd', '3rd', '4th', '5th']);
            $table->enum('semester', ['1st', '2nd', 'summer']);
            $table->string('prerequisites')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
