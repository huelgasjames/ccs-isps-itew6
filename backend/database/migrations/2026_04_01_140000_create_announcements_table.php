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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('excerpt');
            $table->longText('content');
            $table->string('type', 50);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->string('author', 255);
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->boolean('target_all')->default(true);
            $table->boolean('target_students')->default(false);
            $table->boolean('target_professors')->default(false);
            $table->boolean('target_admins')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'publish_date']);
            $table->index(['priority', 'status']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
