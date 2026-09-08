<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_milestones', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('year');
            $table->index(['year', 'month', 'sort_order'], 'company_milestones_period_index');
        });
    }

    public function down(): void
    {
        Schema::table('company_milestones', function (Blueprint $table) {
            $table->dropIndex('company_milestones_period_index');
            $table->dropColumn('month');
        });
    }
};
