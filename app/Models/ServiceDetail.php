<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_detail';
    protected $fillable = ['service_id', 'title', 'subtitle', 'price', 'description', 'created_by', 'updated_by', 'deleted_by'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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
