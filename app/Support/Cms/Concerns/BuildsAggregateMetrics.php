<?php

namespace App\Support\Cms\Concerns;

trait BuildsAggregateMetrics
{
    protected function recentSince(): string
    {
        return now()->subDays(30)->toDateTimeString();
    }

    protected function weekSince(): string
    {
        return now()->subDays(7)->toDateTimeString();
    }

    /**
     * @param  array<int, string>  $keys
     * @return array<string, int>
     */
    protected function castCounts(?object $row, array $keys): array
    {
        $metrics = [];

        foreach ($keys as $key) {
            $metrics[$key] = (int) ($row?->{$key} ?? 0);
        }

        return $metrics;
    }
}
