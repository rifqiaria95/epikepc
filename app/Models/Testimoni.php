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

        return $url ?: asset('frontend/img/testimonial/testi-avatar-1.png');
    }

    /**
     * Query testimonials for homepage display.
     */
    public function scopeForHomepage($query, int $limit = 10)
    {
        return $query
            ->select(['id', 'nama', 'testimoni', 'instansi', 'gambar', 'created_at'])
            ->withoutTrashed()
            ->orderByDesc('created_at')
            ->limit($limit);
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
