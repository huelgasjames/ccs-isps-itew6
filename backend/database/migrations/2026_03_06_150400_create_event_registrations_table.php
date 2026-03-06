<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'attended', 'absent', 'cancelled'])->default('registered');
            $table->timestamp('registration_time');
            $table->text('notes')->nullable();
            $table->decimal('payment_status', 8, 2)->default(0);
            $table->boolean('payment_completed')->default(false);
            $table->timestamps();
            
            $table->unique(['event_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
