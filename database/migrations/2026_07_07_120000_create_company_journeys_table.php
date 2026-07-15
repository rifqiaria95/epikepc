<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_journeys', function (Blueprint $table) {
            $table->id();
            $table->string('section_subtitle')->default('Our Story');
            $table->string('section_title')->default('Company Journey');
            $table->string('section_title_highlight')->default('Journey');
            $table->text('section_description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_poster')->nullable();
            $table->string('video_poster_tag')->nullable();
            $table->string('video_poster_title')->nullable();
            $table->string('video_established')->nullable();
            $table->string('video_location')->nullable();
            $table->string('video_caption')->nullable();
            $table->string('video_duration')->nullable();
            $table->string('timeline_subtitle')->default('Company History');
            $table->string('timeline_title')->default('Our Milestones');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_journeys');
    }
};
