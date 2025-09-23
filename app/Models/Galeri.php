<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Galeri extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'galeri';
    protected $fillable = ['title', 'subtitle', 'description', 'image', 'created_by', 'updated_by', 'deleted_by', 'kategori_galeri_id'];
    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function kategoriGaleri()
    {
        return $this->belongsTo(KategoriGaleri::class);
    }
}
