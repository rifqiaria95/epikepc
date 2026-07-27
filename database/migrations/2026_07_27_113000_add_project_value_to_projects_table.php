<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('project_value')
                ->nullable()
                ->after('project_date')
                ->comment('Project contract/portfolio value for ranking (highest first)');

            $table->index(['is_published', 'project_value'], 'projects_published_value_index');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_published_value_index');
            $table->dropColumn('project_value');
        });
    }
};
