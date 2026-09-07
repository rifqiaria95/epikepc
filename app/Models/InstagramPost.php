<?php

namespace App\Models;

use App\Enums\InstagramMediaType;
use App\Enums\InstagramPostStatus;
use Database\Factories\InstagramPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InstagramPost extends Model
{
    /** @use HasFactory<InstagramPostFactory> */
    use HasFactory;

    protected $fillable = [
        'media_type',
        'caption',
        'media_path',
        'media_url',
        'status',
        'scheduled_at',
        'published_at',
        'container_id',
        'external_media_id',
        'last_error_class',
        'last_error_message',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => InstagramMediaType::class,
            'status' => InstagramPostStatus::class,
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query
            ->where('status', InstagramPostStatus::Scheduled)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }

    public function scopeQueued(Builder $query): Builder
    {
        return $query->whereIn('status', [
            InstagramPostStatus::Draft->value,
            InstagramPostStatus::Scheduled->value,
            InstagramPostStatus::Publishing->value,
            InstagramPostStatus::Failed->value,
        ]);
    }

    public function publicMediaUrl(): ?string
    {
        if (is_string($this->media_url) && preg_match('#^https?://#i', $this->media_url)) {
            return $this->media_url;
        }

        if (! $this->media_path) {
            return null;
        }

        $relative = Storage::disk('public')->url(ltrim($this->media_path, '/'));

        return url($relative);
    }

    public function isVideo(): bool
    {
        return $this->media_type?->isVideo() ?? false;
    }
}
