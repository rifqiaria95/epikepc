<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status', 20)
                ->default('completed')
                ->after('is_published')
                ->index();

            $table->string('location')
                ->nullable()
                ->after('category');

            $table->decimal('latitude', 10, 7)
                ->nullable()
                ->after('location');

            $table->decimal('longitude', 10, 7)
                ->nullable()
                ->after('latitude');

            $table->index(['is_published', 'status']);
            $table->index(['is_published', 'latitude', 'longitude'], 'projects_published_coords_index');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'status']);
            $table->dropIndex('projects_published_coords_index');
            $table->dropColumn(['status', 'location', 'latitude', 'longitude']);
        });
    }
};
