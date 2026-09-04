<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('issuer');
            $table->text('description')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('credential_url', 2048)->nullable();
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('image_alt');
            $table->string('status', 20)->default('DRAFT');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('display_order')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['status', 'display_order']);
            $table->index('is_featured');
            $table->index('display_order');
            $table->index('issued_at');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
