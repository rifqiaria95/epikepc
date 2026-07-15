<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class CoverageLocation extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_DUKUH = 'dukuh';

    public const TYPE_PERUMAHAN = 'perumahan';

    public const TYPE_REFERENCE = 'reference';

    public const CACHE_INDEX_KEY = 'coverage.locations.index';

    public const CACHE_STATS_KEY = 'coverage.locations.stats';

    public const CACHE_REFERENCE_KEY = 'coverage.locations.reference';

    protected $fillable = [
        'kabupaten',
        'kelurahan',
        'name',
        'type',
        'search_key',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (CoverageLocation $location) {
            $location->search_key = static::buildSearchKey(
                $location->kabupaten,
                $location->kelurahan,
                $location->name
            );
        });

        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_INDEX_KEY);
        Cache::forget(self::CACHE_STATS_KEY);
        Cache::forget(self::CACHE_REFERENCE_KEY);
    }

    public static function buildSearchKey(?string $kabupaten, ?string $kelurahan, string $name): string
    {
        return static::normalizeText(implode(' ', array_filter([
            $kabupaten,
            $kelurahan,
            $name,
            str_replace('/', ' ', $name),
        ])));
    }

    public static function normalizeText(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace(['/', '-', '_'], ' ', $value);
        $value = preg_replace('/[^a-z0-9\s]/u', '', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCovered($query)
    {
        return $query->whereIn('type', [self::TYPE_DUKUH, self::TYPE_PERUMAHAN]);
    }

    public function scopeReference($query)
    {
        return $query->where('type', self::TYPE_REFERENCE);
    }

    public function scopeForMatchingIndex($query)
    {
        return $query
            ->select(['id', 'kabupaten', 'kelurahan', 'name', 'type', 'search_key'])
            ->active()
            ->covered()
            ->orderBy('sort_order')
            ->orderBy('kabupaten')
            ->orderBy('kelurahan')
            ->orderBy('name');
    }

    public function scopeForAdminListing($query)
    {
        return $query
            ->select([
                'id',
                'kabupaten',
                'kelurahan',
                'name',
                'type',
                'is_active',
                'sort_order',
                'created_by',
                'created_at',
            ])
            ->with(['createdBy:id,name']);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PERUMAHAN => 'Perumahan',
            self::TYPE_REFERENCE => 'Referensi',
            default => 'Dukuh',
        };
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->type === self::TYPE_REFERENCE) {
            return $this->name;
        }

        return sprintf(
            '%s %s, Kel. %s, %s',
            $this->type_label,
            $this->name,
            $this->kelurahan,
            $this->kabupaten
        );
    }
}
