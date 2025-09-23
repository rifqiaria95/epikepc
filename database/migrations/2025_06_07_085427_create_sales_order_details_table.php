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
        Schema::create('sales_order_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_so')->onDelete('cascade');
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->integer('qty_sod');
            $table->decimal('harga_sod', 15, 2);
            $table->decimal('subtotal_sod', 15, 2);
            $table->string('keterangan_sod')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_detail');
    }
};
