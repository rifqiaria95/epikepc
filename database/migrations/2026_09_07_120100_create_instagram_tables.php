<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->unsignedTinyInteger('feed_limit')->default(6);
            $table->string('eyebrow')->default('Social Media');
            $table->string('heading')->default('Follow Our Journey');
            $table->string('subtitle', 500)->nullable();
            $table->string('cta_label')->default('Follow us on Instagram');
            $table->string('view_more_label')->default('View more on Instagram');
            $table->string('profile_url', 2048)->nullable();
            $table->string('username')->nullable();
            $table->string('profile_picture_url', 2048)->nullable();
            $table->string('account_id')->nullable();
            $table->string('token_status', 32)->default('unknown');
            $table->string('api_status', 32)->default('unknown');
            $table->timestamp('last_successful_feed_sync_at')->nullable();
            $table->timestamp('last_successful_story_sync_at')->nullable();
            $table->timestamp('last_failed_sync_at')->nullable();
            $table->string('last_error_class', 32)->nullable();
            $table->string('last_error_message', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('instagram_media', function (Blueprint $table) {
            $table->id();
            $table->string('external_media_id')->unique();
            $table->string('media_type', 32);
            $table->text('caption')->nullable();
            $table->text('media_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->text('permalink')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_story')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('like_count')->nullable();
            $table->unsignedInteger('comments_count')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['is_story', 'is_visible', 'published_at']);
            $table->index(['is_story', 'is_visible', 'sort_order']);
            $table->index(['is_story', 'expires_at']);
        });

        Schema::create('instagram_media_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instagram_media_id')->constrained('instagram_media')->cascadeOnDelete();
            $table->string('external_media_id');
            $table->string('media_type', 32);
            $table->text('media_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('external_media_id');
            $table->index(['instagram_media_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_media_children');
        Schema::dropIfExists('instagram_media');
        Schema::dropIfExists('instagram_settings');
    }
};
