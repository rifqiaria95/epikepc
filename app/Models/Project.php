<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
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
        'location',
        'latitude',
        'longitude',
        'project_date',
        'project_value',
        'website_url',
        'image',
        'image_secondary',
        'image_tertiary',
        'is_published',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'project_date' => 'date',
        'project_value' => 'integer',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'status' => ProjectStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }

            if (empty($project->status)) {
                $project->status = ProjectStatus::Completed;
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

    public function getStatusLabelAttribute(): string
    {
        $status = $this->status instanceof ProjectStatus
            ? $this->status
            : ProjectStatus::tryFromMixed($this->status) ?? ProjectStatus::Completed;

        return $status->label();
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
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
     * Shared portfolio ordering: highest project value first.
     */
    public function scopeOrderedByPortfolioValue(Builder $query): Builder
    {
        return $query
            ->orderByDesc('project_value')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    /**
     * Query published projects for homepage display.
     * Defaults to the 4 largest portfolio projects.
     */
    public function scopeForHomepage(Builder $query, int $limit = 4): Builder
    {
        return $query
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'category',
                'location',
                'status',
                'project_value',
                'image',
                'sort_order',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_published', true)
            ->orderedByPortfolioValue()
            ->limit($limit);
    }

    /**
     * Query published projects for listing page.
     */
    public function scopeForListing(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'category',
                'location',
                'status',
                'project_value',
                'image',
                'project_date',
                'sort_order',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_published', true)
            ->orderedByPortfolioValue();
    }

    /**
     * Query published projects that can be plotted on a map.
     */
    public function scopeForMap(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'category',
                'location',
                'latitude',
                'longitude',
                'status',
                'project_date',
                'project_value',
                'image',
                'sort_order',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_published', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderedByPortfolioValue();
    }

    /**
     * Filter by lifecycle status when a valid value is provided.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        $resolved = ProjectStatus::tryFromMixed($status);

        if (! $resolved) {
            return $query;
        }

        return $query->where('status', $resolved->value);
    }

    /**
     * Query single project detail with minimal columns.
     */
    public function scopeForDetail(Builder $query): Builder
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
                'location',
                'latitude',
                'longitude',
                'status',
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
