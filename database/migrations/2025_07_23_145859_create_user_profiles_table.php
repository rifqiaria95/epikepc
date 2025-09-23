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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('kk', 16);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('kode_pos', 5);
            $table->string('jenis_kelamin');
            $table->foreignId('id_kecamatan')->constrained(config('laravolt.indonesia.table_prefix').'districts', 'id');
            $table->foreignId('id_kelurahan')->constrained(config('laravolt.indonesia.table_prefix').'villages', 'id');
            $table->foreignId('id_kota')->constrained(config('laravolt.indonesia.table_prefix').'cities', 'id_kota');
            $table->foreignId('id_provinsi')->constrained(config('laravolt.indonesia.table_prefix').'provinces', 'id_provinsi');
            $table->string('no_hp');
            $table->string('pekerjaan')->nullable();
            $table->bigInteger('penghasilan')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_kk')->nullable();
            $table->enum('status_verifikasi', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('verifikasi_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
