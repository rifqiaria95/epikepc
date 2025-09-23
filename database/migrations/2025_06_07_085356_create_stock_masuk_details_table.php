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
        Schema::create('stock_masuk_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sm')->onDelete('cascade');
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->integer('qty_smd');
            $table->decimal('harga_smd', 15, 2);
            $table->decimal('subtotal_smd', 15, 2);
            $table->string('keterangan_smd')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_masuk_detail');
    }
};
