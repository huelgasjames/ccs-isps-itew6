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
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->text('content');
            $table->string('image')->nullable();
            $table->enum('type', ['System', 'Academic', 'Event', 'Feature', 'Policy', 'General'])->default('General');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('target_users')->nullable();
            $table->enum('target_type', ['all', 'students', 'professors', 'specific'])->default('all');
            $table->boolean('target_all')->default(true);
            $table->boolean('target_students')->default(false);
            $table->boolean('target_professors')->default(false);
            $table->boolean('target_admins')->default(false);
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamps();
        });

        Schema::create('announcement_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('viewed_at')->useCurrent();
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_views');
        Schema::dropIfExists('announcements');
    }
};
