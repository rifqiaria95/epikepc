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
        Schema::create('stok_keluar_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sk')->onDelete('cascade');
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->integer('qty_skd');
            $table->decimal('harga_skd', 15, 2);
            $table->decimal('subtotal_skd', 15, 2);
            $table->string('keterangan_skd')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluar_detail');
    }
};
