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
        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_po')->onDelete('cascade');
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->integer('qty_pod');
            $table->decimal('harga_pod', 15, 2);
            $table->decimal('subtotal_pod', 15, 2);
            $table->string('keterangan_pod')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_detail');
    }
};
