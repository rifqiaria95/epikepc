<?php

namespace App\Models;

use App\Enums\InstagramMediaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstagramMediaChild extends Model
{
    use HasFactory;

    protected $table = 'instagram_media_children';

    protected $fillable = [
        'instagram_media_id',
        'external_media_id',
        'media_type',
        'media_url',
        'thumbnail_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => InstagramMediaType::class,
            'sort_order' => 'integer',
        ];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(InstagramMedia::class, 'instagram_media_id');
    }
}
