<?php

namespace App\Services;

use App\Models\CoverageLocation;
use Illuminate\Support\Facades\Cache;

class CoverageCheckService
{
    private ?array $index = null;

    /** @var ?array<int, string> */
    private ?array $referencePlaces = null;

    public function __construct(
        private readonly GeocodingService $geocodingService
    ) {
    }

    /**
     * @return array{kabupaten:int,kelurahan:int,coverage_points:int}
     */
    public function getStats(): array
    {
        return Cache::remember(CoverageLocation::CACHE_STATS_KEY, 3600, function () {
            $rows = CoverageLocation::query()
                ->active()
                ->covered()
                ->select(['kabupaten', 'kelurahan'])
                ->get();

            return [
                'kabupaten' => $rows->pluck('kabupaten')->filter()->unique()->count(),
                'kelurahan' => $rows
                    ->map(fn ($row) => $row->kabupaten . '|' . $row->kelurahan)
                    ->unique()
                    ->count(),
                'coverage_points' => $rows->count(),
            ];
        });
    }

    public function check(string $query): array
    {
        $normalizedQuery = CoverageLocation::normalizeText($query);

        if ($normalizedQuery === '') {
            return [
                'covered' => false,
                'message' => 'Please enter neighborhood, village, or housing name.',
                'match' => null,
                'suggestions' => [],
            ];
        }

        $this->buildIndex();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($this->index as $entry) {
            $score = $this->scoreMatch($normalizedQuery, $entry);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $entry;
            }
        }

        if ($bestMatch !== null && $bestScore >= 60) {
            return [
                'covered' => true,
                'message' => 'Kabar baik! Lokasi Anda berada dalam area jangkauan jaringan kami.',
                'match' => $this->formatMatch($bestMatch),
                'suggestions' => [],
            ];
        }

        $suggestions = $this->buildSuggestions($normalizedQuery);

        return [
            'covered' => false,
            'message' => $suggestions === []
                ? 'Maaf, lokasi belum tercover. Coba periksa ejaan or hubungi tim kami untuk konfirmasi.'
                : 'Lokasi belum tercover. Mungkin maksud Anda salah satu lokasi berikut?',
            'match' => null,
            'suggestions' => $suggestions,
        ];
    }

    public function checkFromCoordinates(float $lat, float $lng): array
    {
        $resolved = $this->geocodingService->reverseGeocode($lat, $lng);

        if ($resolved === null) {
            return [
                'covered' => false,
                'message' => 'Tidak dapat mengenali alamat dari koordinat yang dipilih.',
                'match' => null,
                'suggestions' => [],
                'resolved_location' => null,
            ];
        }

        $result = $this->check($resolved['value']);
        $result['resolved_location'] = $resolved;

        return $result;
    }

    public function suggest(string $query, int $limit = 8): array
    {
        $normalizedQuery = CoverageLocation::normalizeText($query);

        if (mb_strlen($normalizedQuery) < 2) {
            return [];
        }

        $localSuggestions = $this->buildAutocompleteSuggestions($normalizedQuery, $limit);
        $referenceSuggestions = $this->buildReferencePlaceSuggestions($normalizedQuery, $limit);
        $mergedLocal = $this->mergeSuggestionItems(array_merge($localSuggestions, $referenceSuggestions), $limit);

        $remaining = max($limit - count($mergedLocal), 0);

        $placeSuggestions = $remaining > 0
            ? $this->geocodingService->searchPlaces($query, $remaining)
            : [];

        return $this->mergeSuggestionItems(array_merge($mergedLocal, $placeSuggestions), $limit);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function mergeSuggestionItems(array $items, int $limit): array
    {
        $merged = [];
        $seen = [];

        foreach ($items as $suggestion) {
            $key = mb_strtolower((string) ($suggestion['value'] ?? $suggestion['label'] ?? ''));

            if ($key === '' || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $merged[] = $suggestion;

            if (count($merged) >= $limit) {
                break;
            }
        }

        return $merged;
    }

    /**
     * @return array<int, string>
     */
    private function getReferencePlaces(): array
    {
        if ($this->referencePlaces !== null) {
            return $this->referencePlaces;
        }

        $this->referencePlaces = Cache::remember(CoverageLocation::CACHE_REFERENCE_KEY, 3600, function () {
            return CoverageLocation::query()
                ->active()
                ->reference()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name')
                ->all();
        });

        return $this->referencePlaces;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildReferencePlaceSuggestions(string $query, int $limit): array
    {
        $suggestions = [];

        foreach ($this->getReferencePlaces() as $place) {
            $placeKey = CoverageLocation::normalizeText($place);

            if (! $this->matchesAutocompleteQuery($query, $placeKey)) {
                continue;
            }

            $suggestions[] = [
                'id' => 'reference:' . md5($place),
                'label' => $place,
                'value' => $place,
                'source' => 'place',
                'lat' => null,
                'lng' => null,
                'subtitle' => 'Wilayah referensi',
            ];
        }

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildAutocompleteSuggestions(string $query, int $limit): array
    {
        $this->buildIndex();

        $scored = [];

        foreach ($this->index as $entry) {
            $score = $this->scoreMatch($query, $entry);

            if ($score >= 30) {
                $formatted = $this->formatMatch($entry);
                $scored[] = [
                    'score' => $score,
                    'item' => [
                        'id' => 'coverage:' . $entry['id'],
                        'label' => $formatted['label'],
                        'value' => $formatted['label'],
                        'source' => 'coverage',
                        'lat' => null,
                        'lng' => null,
                        'subtitle' => 'Area jangkauan SFX NET',
                    ],
                ];
            }
        }

        $districtRows = collect($this->index)
            ->groupBy(fn ($entry) => $entry['kabupaten'] . '|' . $entry['kelurahan']);

        foreach ($districtRows as $rows) {
            $entry = $rows->first();
            $kabupatenKey = $entry['kabupaten_key'];
            $kelurahanKey = $entry['kelurahan_key'];
            $combinedKey = CoverageLocation::normalizeText($entry['kelurahan'] . ' ' . $entry['kabupaten']);

            if ($this->matchesAutocompleteQuery($query, $kabupatenKey)) {
                $scored[] = [
                    'score' => 72,
                    'item' => [
                        'id' => 'kabupaten:' . $kabupatenKey,
                        'label' => $entry['kabupaten'],
                        'value' => $entry['kabupaten'],
                        'source' => 'coverage',
                        'lat' => null,
                        'lng' => null,
                        'subtitle' => 'Regencies/Kota coverage',
                    ],
                ];
            }

            if ($this->matchesAutocompleteQuery($query, $kelurahanKey) || $this->matchesAutocompleteQuery($query, $combinedKey)) {
                $scored[] = [
                    'score' => 78,
                    'item' => [
                        'id' => 'kelurahan:' . md5($entry['kabupaten'] . $entry['kelurahan']),
                        'label' => sprintf('Kel. %s, %s', $entry['kelurahan'], $entry['kabupaten']),
                        'value' => sprintf('%s, %s', $entry['kelurahan'], $entry['kabupaten']),
                        'source' => 'coverage',
                        'lat' => null,
                        'lng' => null,
                        'subtitle' => 'Villages coverage',
                    ],
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $suggestions = [];
        $seen = [];

        foreach ($scored as $row) {
            $key = $row['item']['id'];

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $suggestions[] = $row['item'];

            if (count($suggestions) >= $limit) {
                break;
            }
        }

        return $suggestions;
    }

    private function matchesAutocompleteQuery(string $query, string $candidate): bool
    {
        if ($candidate === '' || $query === '') {
            return false;
        }

        return str_starts_with($candidate, $query)
            || str_contains($candidate, $query)
            || str_contains($query, $candidate);
    }

    private function buildIndex(): void
    {
        if ($this->index !== null) {
            return;
        }

        $this->index = Cache::remember(CoverageLocation::CACHE_INDEX_KEY, 3600, function () {
            return CoverageLocation::forMatchingIndex()
                ->get()
                ->map(function (CoverageLocation $location) {
                    $name = str_replace('/', ' ', $location->name);

                    return [
                        'id' => $location->id,
                        'kabupaten' => $location->kabupaten,
                        'kelurahan' => $location->kelurahan,
                        'name' => $location->name,
                        'type' => $location->type,
                        'search_key' => $location->search_key,
                        'name_key' => CoverageLocation::normalizeText($name),
                        'kelurahan_key' => CoverageLocation::normalizeText((string) $location->kelurahan),
                        'kabupaten_key' => CoverageLocation::normalizeText((string) $location->kabupaten),
                    ];
                })
                ->all();
        });
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function scoreMatch(string $query, array $entry): int
    {
        if ($query === $entry['name_key']) {
            return 100;
        }

        if ($query === $entry['search_key']) {
            return 95;
        }

        if (str_contains($entry['name_key'], $query) || str_contains($query, $entry['name_key'])) {
            return 85;
        }

        if (str_contains($entry['search_key'], $query)) {
            return 80;
        }

        $queryTokens = array_filter(explode(' ', $query));
        $matchedTokens = 0;

        foreach ($queryTokens as $token) {
            if (strlen($token) < 3) {
                continue;
            }

            if (
                str_contains($entry['name_key'], $token)
                || str_contains($entry['kelurahan_key'], $token)
                || str_contains($entry['kabupaten_key'], $token)
                || str_contains($entry['search_key'], $token)
            ) {
                $matchedTokens++;
            }
        }

        if ($matchedTokens === 0) {
            return 0;
        }

        $tokenScore = (int) round(($matchedTokens / max(count($queryTokens), 1)) * 70);

        if (
            str_contains($query, $entry['kelurahan_key'])
            && str_contains($query, $entry['name_key'])
        ) {
            $tokenScore = max($tokenScore, 75);
        }

        return $tokenScore;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildSuggestions(string $query): array
    {
        $scored = [];

        foreach ($this->index as $entry) {
            $score = $this->scoreMatch($query, $entry);

            if ($score >= 35) {
                $scored[] = ['score' => $score, 'entry' => $entry];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $suggestions = [];
        $seen = [];

        foreach ($scored as $item) {
            $formatted = $this->formatMatch($item['entry']);
            $key = $formatted['label'];

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $suggestions[] = $formatted;

            if (count($suggestions) >= 5) {
                break;
            }
        }

        return $suggestions;
    }

    /**
     * @param array<string, mixed> $entry
     * @return array<string, string>
     */
    private function formatMatch(array $entry): array
    {
        $typeLabel = $entry['type'] === CoverageLocation::TYPE_PERUMAHAN ? 'Perumahan' : 'Dukuh';

        return [
            'kabupaten' => $entry['kabupaten'],
            'kelurahan' => $entry['kelurahan'],
            'name' => $entry['name'],
            'type' => $entry['type'],
            'label' => sprintf(
                '%s %s, Kel. %s, %s',
                $typeLabel,
                $entry['name'],
                $entry['kelurahan'],
                $entry['kabupaten']
            ),
        ];
    }
}
