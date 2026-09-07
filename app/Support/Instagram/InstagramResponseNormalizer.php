<?php

namespace App\Support\Instagram;

use App\Enums\InstagramErrorClass;
use App\Enums\InstagramMediaType;
use App\Exceptions\Instagram\InstagramApiException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class InstagramResponseNormalizer
{
    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function media(array $item, bool $isStory = false): array
    {
        $id = trim((string) ($item['id'] ?? ''));

        if ($id === '') {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Instagram media is missing an id.',
            );
        }

        $publishedAt = $this->timestamp($item['timestamp'] ?? null);
        $expiresAt = $isStory
            ? ($publishedAt ?: now())->copy()->addHours((int) config('instagram.story_ttl_hours', 24))
            : null;

        $children = [];
        foreach (Arr::get($item, 'children.data', []) as $index => $child) {
            if (! is_array($child)) {
                continue;
            }

            $normalizedChild = $this->child($child, (int) $index);
            if ($normalizedChild !== null) {
                $children[] = $normalizedChild;
            }
        }

        return [
            'external_media_id' => $id,
            'media_type' => $this->mediaType($item)->value,
            'caption' => isset($item['caption']) ? (string) $item['caption'] : null,
            'media_url' => $this->safeUrl($item['media_url'] ?? null),
            'thumbnail_url' => $this->safeUrl($item['thumbnail_url'] ?? $item['media_url'] ?? null),
            'permalink' => $this->safeUrl($item['permalink'] ?? null),
            'published_at' => $publishedAt,
            'expires_at' => $expiresAt,
            'is_story' => $isStory,
            'like_count' => $this->unsignedInt($item['like_count'] ?? null),
            'comments_count' => $this->unsignedInt($item['comments_count'] ?? null),
            'children' => $children,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    public function child(array $item, int $sortOrder = 0): ?array
    {
        $id = trim((string) ($item['id'] ?? ''));

        if ($id === '') {
            return null;
        }

        return [
            'external_media_id' => $id,
            'media_type' => $this->mediaType($item)->value,
            'media_url' => $this->safeUrl($item['media_url'] ?? null),
            'thumbnail_url' => $this->safeUrl($item['thumbnail_url'] ?? $item['media_url'] ?? null),
            'sort_order' => $sortOrder,
        ];
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return array<string, mixed>
     */
    public function profile(array $profile): array
    {
        $id = trim((string) ($profile['id'] ?? ''));
        $username = ltrim(trim((string) ($profile['username'] ?? '')), '@');

        if ($id === '' && $username === '') {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Instagram profile response is missing identity fields.',
            );
        }

        $profileUrl = $username !== ''
            ? 'https://www.instagram.com/'.$username.'/'
            : null;

        return [
            'account_id' => $id !== '' ? $id : null,
            'username' => $username !== '' ? $username : null,
            'profile_picture_url' => $this->safeUrl($profile['profile_picture_url'] ?? null),
            'profile_url' => $profileUrl,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public function mediaType(array $item): InstagramMediaType
    {
        $product = strtoupper((string) ($item['media_product_type'] ?? ''));
        $type = strtoupper((string) ($item['media_type'] ?? 'IMAGE'));

        if ($product === 'REELS' || $type === 'REELS') {
            return InstagramMediaType::Reels;
        }

        return match ($type) {
            'VIDEO' => InstagramMediaType::Video,
            'CAROUSEL_ALBUM' => InstagramMediaType::CarouselAlbum,
            default => InstagramMediaType::Image,
        };
    }

    public function timestamp(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public function safeUrl(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $url = trim($value);

        if ($url === '') {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }

    protected function unsignedInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $int = (int) $value;

        return $int >= 0 ? $int : null;
    }
}
