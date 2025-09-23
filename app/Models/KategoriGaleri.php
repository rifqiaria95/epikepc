<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriGaleri extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori_galeri';
    protected $fillable = ['name', 'slug'];

    public function galeri()
    {
        return $this->hasMany(Galeri::class, 'kategori_galeri_id');
    }
}
