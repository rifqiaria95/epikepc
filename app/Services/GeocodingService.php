<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeocodingService
{
    private const CACHE_TTL_SECONDS = 3600;

  /**
     * @return array<int, array{
     *     id: string,
     *     label: string,
     *     value: string,
     *     source: string,
     *     lat: ?float,
     *     lng: ?float,
     *     subtitle: ?string
     * }>
     */
    public function searchPlaces(string $query, int $limit = 6): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        $cacheKey = 'geocode.search.' . md5(mb_strtolower($query) . '|' . $limit);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($query, $limit) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders($this->headers())
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'json',
                        'addressdetails' => 1,
                        'countrycodes' => 'id',
                        'limit' => $limit,
                        'accept-language' => 'id',
                    ]);

                if (! $response->successful()) {
                    return [];
                }

                return collect($response->json())
                    ->map(fn (array $item) => $this->formatPlaceResult($item))
                    ->filter()
                    ->values()
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * @return ?array{
     *     id: string,
     *     label: string,
     *     value: string,
     *     source: string,
     *     lat: float,
     *     lng: float,
     *     subtitle: ?string,
     *     address: array<string, mixed>
     * }
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $cacheKey = 'geocode.reverse.' . md5($lat . '|' . $lng);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($lat, $lng) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders($this->headers())
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat' => $lat,
                        'lon' => $lng,
                        'format' => 'json',
                        'addressdetails' => 1,
                        'accept-language' => 'id',
                    ]);

                if (! $response->successful()) {
                    return null;
                }

                $item = $response->json();

                if (! is_array($item) || empty($item['display_name'])) {
                    return null;
                }

                $formatted = $this->formatPlaceResult($item);

                if ($formatted === null) {
                    return null;
                }

                return array_merge($formatted, [
                    'address' => $item['address'] ?? [],
                ]);
            } catch (\Throwable) {
                return null;
            }
        });
    }

    /**
     * @param array<string, mixed> $item
     * @return ?array{
     *     id: string,
     *     label: string,
     *     value: string,
     *     source: string,
     *     lat: ?float,
     *     lng: ?float,
     *     subtitle: ?string
     * }
     */
    private function formatPlaceResult(array $item): ?array
    {
        $displayName = (string) ($item['display_name'] ?? '');

        if ($displayName === '') {
            return null;
        }

        $lat = isset($item['lat']) ? (float) $item['lat'] : null;
        $lng = isset($item['lon']) ? (float) $item['lon'] : null;
        $placeId = (string) ($item['place_id'] ?? Str::uuid());

        return [
            'id' => 'place:' . $placeId,
            'label' => $this->shortLabel($displayName),
            'value' => $displayName,
            'source' => 'place',
            'lat' => $lat,
            'lng' => $lng,
            'subtitle' => $this->buildSubtitle($item['address'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $address
     */
    private function buildSubtitle(array $address): ?string
    {
        $parts = array_filter([
            $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
            $address['state'] ?? $address['region'] ?? null,
            $address['country'] ?? null,
        ]);

        return $parts === [] ? null : implode(', ', $parts);
    }

    private function shortLabel(string $displayName): string
    {
        $parts = array_map('trim', explode(',', $displayName));

        return implode(', ', array_slice($parts, 0, 3));
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'User-Agent' => config('coverage_areas.geocoding.user_agent', 'SFX-NET-Coverage/1.0'),
            'Accept' => 'application/json',
        ];
    }
}
