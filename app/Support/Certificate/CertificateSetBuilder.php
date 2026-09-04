<?php

namespace App\Support\Certificate;

class CertificateSetBuilder
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function buildSets(array $items, int $viewportWidth): array
    {
        if ($items === []) {
            return [];
        }

        $size = max(1, $this->setSizeForViewport($viewportWidth));
        $sets = array_chunk($items, $size);
        $result = [];

        foreach ($sets as $setIndex => $set) {
            $slots = $this->slotDefinitions(count($set));
            $presented = [];

            foreach ($set as $index => $item) {
                $slot = $slots[$index % count($slots)];
                $presented[] = array_merge($item, [
                    'slot' => $slot,
                    'set_index' => $setIndex,
                ]);
            }

            $result[] = $presented;
        }

        return $result;
    }

    public function setSizeForViewport(int $width): int
    {
        $breakpoints = collect(config('certificates.set_sizes'))
            ->sortByDesc(fn ($item) => $item['min_width'] ?? 0);

        foreach ($breakpoints as $breakpoint) {
            if ($width >= ($breakpoint['min_width'] ?? 0)) {
                return (int) $breakpoint['size'];
            }
        }

        return 3;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function slotDefinitions(int $count): array
    {
        $base = [
            ['zone' => 'upper-left'],
            ['zone' => 'top-center-left'],
            ['zone' => 'top-center-right'],
            ['zone' => 'upper-right'],
            ['zone' => 'mid-left'],
            ['zone' => 'mid-right'],
            ['zone' => 'lower-left'],
            ['zone' => 'lower-right'],
        ];

        return array_slice($base, 0, max(1, min($count, count($base))));
    }

    public function findSetIndexForCertificate(array $sets, string $certificateId): int
    {
        foreach ($sets as $index => $set) {
            foreach ($set as $item) {
                if (($item['id'] ?? null) === $certificateId) {
                    return $index;
                }
            }
        }

        return 0;
    }
}
