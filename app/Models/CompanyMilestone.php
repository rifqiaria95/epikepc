<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class CompanyMilestone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year',
        'month',
        'title',
        'description',
        'sort_order',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'period_label',
        'month_label',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getMonthLabelAttribute(): ?string
    {
        if (! $this->month || $this->month < 1 || $this->month > 12) {
            return null;
        }

        return Carbon::create()->month($this->month)->format('M');
    }

    public function getPeriodLabelAttribute(): string
    {
        $year = (string) ($this->year ?? '');

        if ($this->month_label) {
            return trim($this->month_label.' '.$year);
        }

        return $year;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('year')
            ->orderByRaw('CASE WHEN month IS NULL THEN 13 ELSE month END');
    }

    public function scopeForHomepage($query)
    {
        return $query
            ->select(['id', 'year', 'month', 'title', 'description', 'sort_order'])
            ->withoutTrashed()
            ->active()
            ->ordered();
    }
}
