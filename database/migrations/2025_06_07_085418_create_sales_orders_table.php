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
        Schema::create('sales_order', function (Blueprint $table) {
            $table->id();
            $table->string('no_so')->unique();
            $table->date('tgl_so');
            $table->date('tgl_approve_so')->nullable();
            $table->date('tgl_reject_so')->nullable();
            $table->enum('status_so', ['draft', 'approved', 'rejected'])->default('draft');
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->unsignedBigInteger('id_pelanggan')->onDelete('cascade');
            $table->unsignedBigInteger('id_gudang')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->onDelete('cascade');
            $table->unsignedBigInteger('approved_by')->nullable()->onDelete('cascade');
            $table->unsignedBigInteger('rejected_by')->nullable()->onDelete('cascade');
            $table->string('keterangan_so')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order');
    }
};
