<?php

namespace App\Queries\Career;

use App\Models\Career\JobVacancy;
use App\Support\Career\QueryLike;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CareerVacancyQuery
{
    use QueryLike;

    public function publicListing(Request $request): LengthAwarePaginator
    {
        $query = JobVacancy::query()
            ->publiclyVisible()
            ->select([
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
                'summary',
                'closes_at',
                'published_at',
                'requires_site_travel',
            ]);

        $this->applyPublicFilters($query, $request);

        return $query
            ->orderByDesc('published_at')
            ->paginate((int) config('career.pagination.public_per_page'))
            ->withQueryString();
    }

    public function findPublicBySlug(string $slug): JobVacancy
    {
        return JobVacancy::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->with(['questions' => fn ($q) => $q->orderBy('sort_order')])
            ->firstOrFail();
    }

    public function relatedPublic(JobVacancy $vacancy, int $limit = 3)
    {
        return JobVacancy::query()
            ->publiclyVisible()
            ->where('id', '!=', $vacancy->id)
            ->where(function (Builder $q) use ($vacancy) {
                $q->where('department', $vacancy->department)
                    ->orWhere('location_city', $vacancy->location_city);
            })
            ->select([
                'id', 'title', 'slug', 'department', 'location_city',
                'location_province', 'employment_type', 'work_arrangement', 'closes_at',
            ])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    public function cmsBaseQuery(Request $request): Builder
    {
        $query = JobVacancy::query()->forCmsListing();

        if ($search = $this->datatablesSearchTerm($request)) {
            $like = $this->likeOperator();
            $query->where(function (Builder $q) use ($search, $like) {
                $q->where('title', $like, "%{$search}%")
                    ->orWhere('code', $like, "%{$search}%")
                    ->orWhere('department', $like, "%{$search}%")
                    ->orWhere('location_city', $like, "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($department = $request->get('department')) {
            $query->where('department', $department);
        }

        if ($employment = $request->get('employment_type')) {
            $query->where('employment_type', $employment);
        }

        if ($arrangement = $request->get('work_arrangement')) {
            $query->where('work_arrangement', $arrangement);
        }

        return $query->orderByDesc('updated_at');
    }

    public function filterOptions(): array
    {
        $base = JobVacancy::query()->publiclyVisible();

        return [
            'departments' => (clone $base)->select('department')->distinct()->orderBy('department')->pluck('department'),
            'locations' => (clone $base)->select('location_city')->distinct()->orderBy('location_city')->pluck('location_city'),
            'employment_types' => (clone $base)->select('employment_type')->distinct()->pluck('employment_type'),
            'work_arrangements' => (clone $base)->select('work_arrangement')->distinct()->pluck('work_arrangement'),
        ];
    }

    protected function applyPublicFilters(Builder $query, Request $request): void
    {
        if ($q = trim((string) $request->get('q', ''))) {
            $like = $this->likeOperator();
            $query->where(function (Builder $builder) use ($q, $like) {
                $builder->where('title', $like, "%{$q}%")
                    ->orWhere('department', $like, "%{$q}%")
                    ->orWhere('summary', $like, "%{$q}%")
                    ->orWhere('location_city', $like, "%{$q}%");
            });
        }

        foreach (['department', 'employment_type', 'work_arrangement'] as $field) {
            if ($value = $request->get($field)) {
                $query->where($field, $value);
            }
        }

        if ($location = $request->get('location')) {
            $query->where(function (Builder $builder) use ($location) {
                $builder->where('location_city', $location)
                    ->orWhere('location_province', $location);
            });
        }
    }

    /** SQLite-compatible search for tests */
    public function applySearchCompatible(Builder $query, string $column, string $term): Builder
    {
        $driver = $query->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            return $query->where($column, 'ilike', "%{$term}%");
        }

        return $query->whereRaw('LOWER('.$column.') LIKE ?', ['%'.mb_strtolower($term).'%']);
    }
}
