<?php

namespace App\Support\Career;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait QueryLike
{
    protected function likeOperator(): string
    {
        return DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
    }

    protected function datatablesSearchTerm(Request $request): ?string
    {
        $search = $request->input('search');

        if (is_array($search)) {
            $value = trim((string) ($search['value'] ?? ''));

            return $value !== '' ? $value : null;
        }

        $value = trim((string) ($search ?? $request->input('q', '')));

        return $value !== '' ? $value : null;
    }

    protected function whereLike(Builder $query, string $column, string $term): Builder
    {
        return $query->where($column, $this->likeOperator(), '%'.$term.'%');
    }

    protected function orWhereLike(Builder $query, string $column, string $term): Builder
    {
        return $query->orWhere($column, $this->likeOperator(), '%'.$term.'%');
    }
}
