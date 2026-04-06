<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Testimoni extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'testimoni';
    protected $fillable = ['nama', 'testimoni', 'instansi', 'gambar', 'created_by', 'updated_by', 'deleted_by'];

    public function getImageUrl()
    {
        if (!$this->gambar) {
            return null;
        }

        return Storage::disk('public')->url($this->gambar);
    }

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    public function getGambarUrlAttribute()
    {
        $url = $this->getImageUrl();
        // Fallback ke default image jika tidak ada gambar
        return $url ?: asset('frontend/img/bg-img/4.jpg');
    }

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
}
