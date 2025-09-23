<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {

        // Tabel vendor
        Schema::create('vendor', function (Blueprint $table) {
            $table->id('id');
            $table->string('nm_vendor');
            $table->text('alamat_vendor');
            $table->string('no_telp_vendor');
            $table->foreignId('id_kota')->constrained(config('laravolt.indonesia.table_prefix').'cities', 'id_kota');
            $table->foreignId('id_provinsi')->constrained(config('laravolt.indonesia.table_prefix').'provinces', 'id_provinsi');
            $table->softDeletes();
            $table->timestamps();
        });

        // Tabel pelanggan
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nm_pelanggan');
            $table->string('alamat_pelanggan');
            $table->string('no_telp_pelanggan');
            $table->string('deskripsi')->nullable();
            $table->foreignId('id_kota')->constrained(config('laravolt.indonesia.table_prefix').'cities', 'id_kota');
            $table->foreignId('id_provinsi')->constrained(config('laravolt.indonesia.table_prefix').'provinces', 'id_provinsi');
            $table->tinyInteger('status');
            $table->softDeletes();
            $table->timestamps();
        });

        // Tabel item
        Schema::create('item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kd_barcode')->nullable();
            $table->string('kd_item')->unique();
            $table->string('nm_item');
            $table->string('deskripsi')->nullable();
            $table->string('foto_item')->nullable();
            $table->unsignedBigInteger('id_unit_berat')->onDelete('cascade');
            $table->unsignedBigInteger('id_kategori')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        // Tabel pegawai
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id('id');
            $table->string('nm_pegawai');
            $table->string('no_telp_pegawai');
            $table->string('foto_pegawai')->nullable();
            $table->foreignId('id_jabatan')->constrained('jabatan');
            $table->foreignId('id_departemen')->constrained('departemen');
            $table->foreignId('id_cabang')->constrained('cabang');
            $table->foreignId('id_perusahaan')->constrained('perusahaan');
            $table->foreignId('id_divisi')->constrained('divisi');
            $table->decimal('gaji_pegawai', 15, 2);
            $table->date('tgl_lahir_pegawai');
            $table->date('tgl_masuk_pegawai');
            $table->date('tgl_keluar_pegawai')->nullable();
            $table->string('no_ktp_pegawai');
            $table->string('no_sim_pegawai')->nullable();
            $table->string('no_npwp_pegawai')->nullable();
            $table->text('alamat_pegawai');
            $table->string('avatar')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('id_kota')->constrained(config('laravolt.indonesia.table_prefix').'cities', 'id_kota');
            $table->foreignId('id_provinsi')->constrained(config('laravolt.indonesia.table_prefix').'provinces', 'id_provinsi');
            $table->tinyInteger('jenis_kelamin');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('item');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('vendor');
    }
};
