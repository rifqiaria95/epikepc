<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pricing';

    protected $fillable = [
        'name',
        'price',
        'billing_period',
        'description',
        'is_popular',
        'is_active',
        'sort_order',
        'button_url',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pricingFeatures()
    {
        return $this->hasMany(PricingFeature::class, 'pricing_id');
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

    /**
     * Query active pricing plans for homepage display.
     */
    public function scopeForHomepage($query, int $limit = 6)
    {
        return $query
            ->select([
                'id',
                'name',
                'price',
                'billing_period',
                'description',
                'is_popular',
                'is_active',
                'sort_order',
                'button_url',
                'created_at',
            ])
            ->withoutTrashed()
            ->where('is_active', true)
            ->with([
                'pricingFeatures' => function ($featureQuery) {
                    $featureQuery
                        ->select(['id', 'pricing_id', 'feature', 'sort_order'])
                        ->withoutTrashed()
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    /**
     * Base query for internal listing with eager-loaded relations.
     */
    public function scopeForAdminListing($query)
    {
        return $query
            ->select([
                'id',
                'name',
                'price',
                'billing_period',
                'description',
                'is_popular',
                'is_active',
                'sort_order',
                'button_url',
                'created_by',
                'created_at',
            ])
            ->withoutTrashed()
            ->withCount('pricingFeatures')
            ->with(['createdBy:id,name']);
    }
}
