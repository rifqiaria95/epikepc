<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationRequest extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'service_name',
        'message',
        'source',
        'status',
        'internal_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function scopeForAdminListing($query)
    {
        return $query
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'service_name',
                'source',
                'status',
                'created_by',
                'created_at',
            ])
            ->with(['createdBy:id,name']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONTACTED => 'Sudah Dihubungi',
            self::STATUS_CLOSED => 'Selesai',
            default => 'Baru',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return $this->source === 'contact' ? 'Halaman Kontak' : 'Homepage';
    }
}
