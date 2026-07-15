<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service', function (Blueprint $table) {
            $table->string('image_secondary')->nullable()->after('image');
            $table->string('image_tertiary')->nullable()->after('image_secondary');
        });
    }

    public function down(): void
    {
        Schema::table('service', function (Blueprint $table) {
            $table->dropColumn(['image_secondary', 'image_tertiary']);
        });
    }
};
