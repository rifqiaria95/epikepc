<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organisasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table    = 'organisasi';
    protected $fillable = ['nama', 'jabatan', 'tahun', 'lokasi', 'deskripsi', 'image', 'created_by'];
    protected $dates    = ['deleted_at'];

    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return asset('storage/' . $value);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
