<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('no_sk')->unique();
            $table->unsignedBigInteger('id_so')->onDelete('cascade');
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->unsignedBigInteger('id_gudang')->onDelete('cascade');
            $table->date('tgl_sk');
            $table->integer('qty_sk');
            $table->string('keterangan_sk')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluar');
    }
};
