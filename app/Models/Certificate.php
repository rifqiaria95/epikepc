<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory;
    use HasUuidPrimaryKey;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'issuer',
        'description',
        'certificate_number',
        'issued_at',
        'expires_at',
        'credential_url',
        'image_path',
        'thumbnail_path',
        'image_alt',
        'status',
        'is_featured',
        'display_order',
        'published_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => CertificateStatus::class,
            'is_featured' => 'boolean',
            'issued_at' => 'date',
            'expires_at' => 'date',
            'published_at' => 'datetime',
            'display_order' => 'integer',
        ];
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

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', CertificateStatus::Published)
            ->where(function (Builder $inner) {
                $inner->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeVisibleOnFrontend(Builder $query): Builder
    {
        $query = $query->published();

        if (! config('certificates.show_expired_on_frontend')) {
            $query->where(function (Builder $inner) {
                $inner->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now()->toDateString());
            });
        }

        return $query;
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('display_order')
            ->orderByDesc('published_at')
            ->orderBy('title');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function canPublish(): bool
    {
        return filled($this->image_path) && filled($this->image_alt);
    }
}
