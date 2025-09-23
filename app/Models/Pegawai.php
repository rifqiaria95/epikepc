<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    /** @use HasFactory<\Database\Factories\PegawaiFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'nm_pegawai',
        'alamat_pegawai',
        'no_telp_pegawai',
        'foto_pegawai',
        'gaji_pegawai',
        'jenis_kelamin',
        'tgl_lahir_pegawai',
        'tgl_masuk_pegawai',
        'tgl_keluar_pegawai',
        'no_ktp_pegawai',
        'no_sim_pegawai',
        'no_npwp_pegawai',
        'id_kota',
        'id_perusahaan',
        'id_divisi',
        'id_cabang',
        'id_departemen',
        'id_jabatan',
        'id_provinsi',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }
}
