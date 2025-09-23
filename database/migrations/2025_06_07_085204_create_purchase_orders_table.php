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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->date('tgl_po');
            $table->date('tgl_approve_po')->nullable();
            $table->date('tgl_reject_po')->nullable();
            $table->unsignedBigInteger('id_item')->onDelete('cascade');
            $table->unsignedBigInteger('id_gudang')->onDelete('cascade');
            $table->unsignedBigInteger('id_vendor')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->onDelete('cascade');
            $table->unsignedBigInteger('approved_by')->nullable()->onDelete('cascade');
            $table->unsignedBigInteger('rejected_by')->nullable()->onDelete('cascade');
            $table->integer('qty_po');
            $table->string('keterangan_po')->nullable();
            $table->enum('status_po', ['draft', 'approved', 'rejected'])->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
