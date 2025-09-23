<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisiMisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'visi_misi';
    protected $fillable = ['title', 'subtitle', 'description', 'image', 'created_by', 'updated_by', 'deleted_by'];

    public function getImageUrl()
    {
        return $this->image ? url('storage/' . $this->image) : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
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
