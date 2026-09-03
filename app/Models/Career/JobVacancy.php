<?php

namespace App\Models\Career;

use App\Enums\Career\EmploymentType;
use App\Enums\Career\ExperienceLevel;
use App\Enums\Career\VacancyStatus;
use App\Enums\Career\WorkArrangement;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use App\Models\User;
use Database\Factories\Career\JobVacancyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobVacancy extends Model
{
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;

    protected static function newFactory(): JobVacancyFactory
    {
        return JobVacancyFactory::new();
    }

    protected $table = 'job_vacancies';

    protected $fillable = [
        'code',
        'title',
        'slug',
        'department',
        'location_city',
        'location_province',
        'employment_type',
        'work_arrangement',
        'experience_level',
        'summary',
        'description',
        'responsibilities',
        'qualifications',
        'preferred_qualifications',
        'minimum_education',
        'minimum_experience_years',
        'headcount',
        'requires_site_travel',
        'allows_salary_expectation',
        'published_at',
        'closes_at',
        'status',
        'seo_title',
        'seo_description',
        'created_by',
        'updated_by',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'work_arrangement' => WorkArrangement::class,
            'experience_level' => ExperienceLevel::class,
            'status' => VacancyStatus::class,
            'requires_site_travel' => 'boolean',
            'allows_salary_expectation' => 'boolean',
            'published_at' => 'datetime',
            'closes_at' => 'datetime',
            'minimum_experience_years' => 'integer',
            'headcount' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function questions(): HasMany
    {
        return $this->hasMany(JobVacancyQuestion::class)->orderBy('sort_order');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', VacancyStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $q) {
                $q->whereNull('closes_at')->orWhere('closes_at', '>', now());
            });
    }

    public function scopeForCmsListing(Builder $query): Builder
    {
        return $query->select([
            'id',
            'code',
            'title',
            'slug',
            'department',
            'location_city',
            'location_province',
            'employment_type',
            'work_arrangement',
            'experience_level',
            'status',
            'published_at',
            'closes_at',
            'created_by',
            'updated_at',
            'created_at',
        ])->withCount('applications')->with(['createdBy:id,name']);
    }

    public function acceptsApplications(): bool
    {
        if ($this->status !== VacancyStatus::Published) {
            return false;
        }

        if (! $this->published_at || $this->published_at->isFuture()) {
            return false;
        }

        if ($this->closes_at && $this->closes_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [VacancyStatus::Draft, VacancyStatus::Published], true);
    }

    public function locationLabel(): string
    {
        return trim($this->location_city.', '.$this->location_province);
    }

    public static function makeSlug(string $title, ?string $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'lowongan';
        $slug = $base;
        $i = 2;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
