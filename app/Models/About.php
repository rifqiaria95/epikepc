<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class About extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'about';
    protected $fillable = ['title', 'subtitle', 'description', 'image', 'video', 'address', 'phone', 'email', 'facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'created_by', 'updated_by', 'deleted_by'];
    protected $keyType = 'string';
    public $incrementing = false;

    // Relasi untuk user yang membuat record ini
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi untuk user yang update record ini
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi untuk user yang delete record ini
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Backward compatibility - menggunakan creator sebagai default user
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
