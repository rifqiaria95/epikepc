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
    protected $fillable = ['title', 'subtitle', 'description', 'image', 'image_secondary', 'image_tertiary', 'service_type_id', 'created_by', 'updated_by', 'deleted_by'];

    public function getImageUrl(): ?string
    {
        return $this->resolvePublicStorageUrl($this->image);
    }

    protected function resolvePublicStorageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalizedPath = ltrim($path, '/');

        if (! Storage::disk('public')->exists($normalizedPath)) {
            return null;
        }

        return Storage::disk('public')->url($normalizedPath);
    }

    public function getImageSecondaryUrl(): ?string
    {
        return $this->resolvePublicStorageUrl($this->image_secondary);
    }

    public function getImageTertiaryUrl(): ?string
    {
        return $this->resolvePublicStorageUrl($this->image_tertiary);
    }

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    public function getImageSecondaryUrlAttribute(): string
    {
        return $this->getImageSecondaryUrl() ?: asset('frontend/img/blog/blog-details-thumb-1-2.png');
    }

    public function getImageTertiaryUrlAttribute(): string
    {
        return $this->getImageTertiaryUrl() ?: asset('frontend/img/blog/blog-details-thumb-1-3.png');
    }

    public function getDetailImageUrlAttribute(): string
    {
        return $this->getImageUrl() ?: asset('frontend/img/service/single-service.png');
    }

    /**
     * Query services for homepage display with eager-loaded relations.
     */
    public function scopeForHomepage($query, int $limit = 8)
    {
        return $query
            ->select(['id', 'title', 'subtitle', 'description', 'image', 'service_type_id', 'created_at'])
            ->withoutTrashed()
            ->with(['serviceType:id,name,slug,type'])
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    /**
     * Query single service detail with eager-loaded relations.
     */
    public function scopeForDetail($query)
    {
        return $query
            ->select([
                'id',
                'title',
                'subtitle',
                'description',
                'image',
                'image_secondary',
                'image_tertiary',
                'service_type_id',
                'created_at',
            ])
            ->withoutTrashed()
            ->with([
                'serviceType:id,name,slug',
                'serviceFeatures' => function ($featureQuery) {
                    $featureQuery
                        ->select(['id', 'service_id', 'feature', 'sort_order'])
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
                'serviceFaqs' => function ($faqQuery) {
                    $faqQuery
                        ->select(['id', 'service_id', 'question', 'answer', 'sort_order'])
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ]);
    }

    /**
     * Query services for sidebar navigation.
     */
    public function scopeForSidebar($query, ?int $excludeId = null)
    {
        return $query
            ->select(['id', 'title'])
            ->withoutTrashed()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->orderBy('title');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
    }

    public function serviceFeatures()
    {
        return $this->hasMany(ServiceFeature::class, 'service_id');
    }

    public function serviceFaqs()
    {
        return $this->hasMany(ServiceFaq::class, 'service_id');
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
