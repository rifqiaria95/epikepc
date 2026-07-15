<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'content_secondary',
        'challenge_solution',
        'final_result',
        'client',
        'category',
        'project_date',
        'website_url',
        'image',
        'image_secondary',
        'image_tertiary',
        'is_published',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'project_date' => 'date',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function getImageUrl(): ?string
    {
        return $this->resolvePublicStorageUrl($this->image);
    }

    public function getImageSecondaryUrl(): ?string
    {
        return $this->resolvePublicStorageUrl($this->image_secondary);
    }

    public function getImageTertiaryUrl(): ?string
    {
        return $this->resolvePublicStorageUrl($this->image_tertiary);
    }

    protected function resolvePublicStorageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalizedPath = ltrim($path, '/');

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (! $disk->exists($normalizedPath)) {
            return null;
        }

        return $disk->url($normalizedPath);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->getImageUrl() ?: asset('frontend/img/project/project-details.png');
    }

    public function getImageSecondaryUrlAttribute(): string
    {
        return $this->getImageSecondaryUrl() ?: asset('frontend/img/blog/blog-details-thumb-1-2.png');
    }

    public function getImageTertiaryUrlAttribute(): string
    {
        return $this->getImageTertiaryUrl() ?: asset('frontend/img/blog/blog-details-thumb-1-3.png');
    }

    public function getFormattedProjectDateAttribute(): ?string
    {
        return $this->project_date?->locale('id')->translatedFormat('d F Y');
    }

    public function getCategoryTagsAttribute(): array
    {
        if (blank($this->category)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->category))));
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Query published projects for homepage display.
     */
    public function scopeForHomepage($query, int $limit = 3)
    {
        return $query
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'category',
                'image',
                'sort_order',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    /**
     * Query published projects for listing page.
     */
    public function scopeForListing($query)
    {
        return $query
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'category',
                'image',
                'project_date',
                'sort_order',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    /**
     * Query single project detail with minimal columns.
     */
    public function scopeForDetail($query)
    {
        return $query
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'content_secondary',
                'challenge_solution',
                'final_result',
                'client',
                'category',
                'project_date',
                'website_url',
                'image',
                'image_secondary',
                'image_tertiary',
                'is_published',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_published', true);
    }
}
