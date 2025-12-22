<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\ServiceType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_type', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });

        // Generate slugs untuk data yang sudah ada
        ServiceType::withTrashed()->get()->each(function ($serviceType) {
            $serviceType->slug = Str::slug($serviceType->name);
            $serviceType->saveQuietly();
        });

        // Setelah semua data punya slug, jadikan not nullable
        Schema::table('service_type', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_type', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

