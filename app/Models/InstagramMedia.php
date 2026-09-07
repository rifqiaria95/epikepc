<?php

namespace App\Models;

use App\Enums\InstagramMediaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstagramMedia extends Model
{
    use HasFactory;

    protected $table = 'instagram_media';

    protected $fillable = [
        'external_media_id',
        'media_type',
        'caption',
        'media_url',
        'thumbnail_url',
        'permalink',
        'published_at',
        'expires_at',
        'is_story',
        'is_visible',
        'sort_order',
        'like_count',
        'comments_count',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => InstagramMediaType::class,
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'synced_at' => 'datetime',
            'is_story' => 'boolean',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
            'like_count' => 'integer',
            'comments_count' => 'integer',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(InstagramMediaChild::class)->orderBy('sort_order');
    }

    public function scopeFeed(Builder $query): Builder
    {
        return $query->where('is_story', false);
    }

    public function scopeStories(Builder $query): Builder
    {
        return $query->where('is_story', true);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeActiveStories(Builder $query): Builder
    {
        return $query
            ->stories()
            ->visible()
            ->where(function (Builder $inner) {
                $inner->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function previewUrl(): ?string
    {
        return $this->thumbnail_url ?: $this->media_url;
    }
}
