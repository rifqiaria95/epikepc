<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_program';
    protected $fillable = ['nama_jenis_program', 'deskripsi_jenis_program', 'gambar_jenis_program', 'status'];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
