<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        // Check if using GCS storage
        $defaultDisk = config('filesystems.default');
        
        if ($defaultDisk === 'gcs') {
            $gcsUrl = config('filesystems.disks.gcs.url');
            $bucket = config('filesystems.disks.gcs.bucket');
            
            // Use custom URL if set, otherwise use default GCS URL
            if (!empty($gcsUrl)) {
                return rtrim($gcsUrl, '/') . '/' . ltrim($this->gambar, '/');
            }
            
            // Default GCS public URL
            return 'https://storage.googleapis.com/' . $bucket . '/' . $this->gambar;
        }

        // Fallback to local storage URL
        return url('storage/' . $this->gambar);
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
