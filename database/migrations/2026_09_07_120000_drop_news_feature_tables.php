<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $this->backupNewsIfPresent();

        Schema::dropIfExists('comments');
        Schema::dropIfExists('news_tag');
        Schema::dropIfExists('category_news');
        Schema::dropIfExists('news');
        Schema::dropIfExists('tags');
    }

    public function down(): void
    {
        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug');
                $table->text('content');
                $table->text('summary')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('path')->nullable();
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->date('published_at')->nullable();
                $table->date('archived_at')->nullable();
                $table->foreignId('author_id')->constrained('users');
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->foreignId('deleted_by')->nullable()->constrained('users');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('category_news')) {
            Schema::create('category_news', function (Blueprint $table) {
                $table->id();
                $table->uuid('news_id');
                $table->foreign('news_id')->references('id')->on('news');
                $table->foreignId('category_id')->constrained('kategori');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('news_tag')) {
            Schema::create('news_tag', function (Blueprint $table) {
                $table->id();
                $table->uuid('news_id');
                $table->foreign('news_id')->references('id')->on('news');
                $table->foreignId('tag_id')->constrained('tags');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->uuid('news_id');
                $table->foreign('news_id')->references('id')->on('news');
                $table->foreignId('user_id')->constrained('users');
                $table->text('comment');
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    protected function backupNewsIfPresent(): void
    {
        if (! Schema::hasTable('news')) {
            return;
        }

        try {
            $news = DB::table('news')->get();

            if ($news->isEmpty()) {
                return;
            }

            $payload = [
                'exported_at' => now()->toIso8601String(),
                'news' => $news,
                'category_news' => Schema::hasTable('category_news') ? DB::table('category_news')->get() : [],
                'news_tag' => Schema::hasTable('news_tag') ? DB::table('news_tag')->get() : [],
                'comments' => Schema::hasTable('comments') ? DB::table('comments')->get() : [],
                'tags' => Schema::hasTable('tags') ? DB::table('tags')->get() : [],
            ];

            Storage::disk('local')->put(
                'backups/news-'.now()->format('YmdHis').'.json',
                json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        } catch (Throwable) {
            // Backup is best-effort and must never block schema cleanup.
        }
    }
};
