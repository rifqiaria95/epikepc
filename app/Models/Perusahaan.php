<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Perusahaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perusahaan';
    protected $guarded = [];

    public function cabang()
    {
        return $this->hasMany(Cabang::class, 'id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_perusahaan');
    }
}
