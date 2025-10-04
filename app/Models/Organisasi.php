<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organisasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table    = 'organisasi';
    protected $fillable = ['nama', 'jabatan', 'tahun', 'lokasi', 'deskripsi', 'image', 'created_by', 'updated_by', 'deleted_by'];
    protected $dates    = ['deleted_at'];

    // Removed getImageAttribute accessor to use FileStorageService instead

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
