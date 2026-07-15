<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coverage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('kabupaten')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('name');
            $table->enum('type', ['dukuh', 'perumahan', 'reference'])->default('dukuh');
            $table->string('search_key')->index();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['kabupaten', 'kelurahan']);
            $table->index(['type', 'is_active']);
            $table->unique(['kabupaten', 'kelurahan', 'name', 'type'], 'coverage_locations_unique_area');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coverage_locations');
    }
};
