<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service';
    protected $fillable = ['title', 'subtitle', 'description', 'image', 'service_type_id', 'created_by', 'updated_by', 'deleted_by'];

    public function getImageUrl()
    {
        if (!$this->image) {
            return null;
        }

        return Storage::disk('public')->url($this->image);
    }

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
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
