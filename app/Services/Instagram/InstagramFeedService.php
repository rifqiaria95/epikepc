<?php

namespace App\Services\Instagram;

use App\Models\InstagramHighlight;
use App\Models\InstagramMedia;
use App\Models\InstagramSetting;
use App\Support\Instagram\InstagramCaptionSanitizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class InstagramFeedService
{
    /**
     * @return array<string, mixed>
     */
    public function widget(): array
    {
        return Cache::remember(
            InstagramSyncService::CACHE_WIDGET,
            min(
                (int) config('instagram.feed_cache_ttl', 3600),
                (int) config('instagram.story_cache_ttl', 300)
            ),
            fn () => $this->buildWidget()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function buildWidget(): array
    {
        $settings = InstagramSetting::current();
        $defaults = config('instagram.defaults');
        $enabled = (bool) config('instagram.enabled') && $settings->enabled;
        $limit = max(1, min((int) $settings->feed_limit, (int) config('instagram.feed_limit', 6)));

        $profileUrl = $this->profileUrl($settings, $defaults['profile_url']);

        $liveStories = $enabled ? $this->liveStories() : collect();
        $highlights = $enabled ? $this->highlights() : collect();
        $rings = $enabled ? $this->rings($liveStories, $highlights) : collect();
        $feed = $enabled ? $this->feed($limit) : collect();

        return [
            'enabled' => $enabled,
            'has_feed' => $feed->isNotEmpty(),
            'has_stories' => $rings->isNotEmpty(),
            'has_live_stories' => $liveStories->isNotEmpty(),
            'has_highlights' => $highlights->isNotEmpty(),
            'eyebrow' => $settings->eyebrow ?: $defaults['eyebrow'],
            'heading' => $settings->heading ?: $defaults['heading'],
            'subtitle' => $settings->subtitle ?: $defaults['subtitle'],
            'cta_label' => $settings->cta_label ?: $defaults['cta_label'],
            'view_more_label' => $settings->view_more_label ?: $defaults['view_more_label'],
            'profile' => [
                'username' => $settings->username,
                'profile_url' => $profileUrl,
                'profile_picture_url' => $settings->profile_picture_url,
            ],
            'stories' => $liveStories->all(),
            'highlights' => $highlights->all(),
            'rings' => $rings->all(),
            'feed' => $feed->all(),
            'last_synced_at' => optional($settings->lastSuccessfulSyncAt())?->toIso8601String(),
            'fallback_image' => asset(ltrim((string) config('instagram.fallback_image'), '/')),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function feed(int $limit): Collection
    {
        $rows = InstagramMedia::query()
            ->select([
                'id',
                'external_media_id',
                'media_type',
                'caption',
                'media_url',
                'thumbnail_url',
                'permalink',
                'published_at',
                'is_visible',
                'sort_order',
                'like_count',
                'comments_count',
            ])
            ->feed()
            ->visible()
            ->with(['children:id,instagram_media_id,external_media_id,media_type,media_url,thumbnail_url,sort_order'])
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        return $rows->map(fn (InstagramMedia $media) => $this->presentFeed($media))->values();
    }

    /**
     * Active 24-hour stories from Instagram API snapshot.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function liveStories(): Collection
    {
        $limit = max(1, (int) config('instagram.story_limit', 10));

        $rows = InstagramMedia::query()
            ->select([
                'id',
                'external_media_id',
                'media_type',
                'caption',
                'media_url',
                'thumbnail_url',
                'permalink',
                'published_at',
                'expires_at',
            ])
            ->activeStories()
            ->orderBy('sort_order')
            ->orderBy('published_at')
            ->limit($limit)
            ->get();

        return $rows->map(fn (InstagramMedia $media) => $this->presentStory($media))->values();
    }

    /**
     * @deprecated Use liveStories(); retained for callers that expect the old name.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function stories(): Collection
    {
        return $this->liveStories();
    }

    /**
     * CMS-managed Story Highlights (Meta Graph API does not expose highlights).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function highlights(): Collection
    {
        $limit = max(1, (int) config('instagram.highlight_limit', 10));

        return InstagramHighlight::query()
            ->visible()
            ->ordered()
            ->limit($limit)
            ->get(['id', 'title', 'cover_url', 'permalink', 'sort_order'])
            ->map(fn (InstagramHighlight $highlight) => $this->presentHighlight($highlight))
            ->values();
    }

    /**
     * Public story row: optional Latest (live) + highlights.
     *
     * @param  Collection<int, array<string, mixed>>  $liveStories
     * @param  Collection<int, array<string, mixed>>  $highlights
     * @return Collection<int, array<string, mixed>>
     */
    public function rings(Collection $liveStories, Collection $highlights): Collection
    {
        $rings = collect();

        if ($highlights->isNotEmpty()) {
            if ($liveStories->isNotEmpty()) {
                $first = $liveStories->first();
                $rings->push([
                    'kind' => 'live',
                    'id' => 'live',
                    'label' => 'Latest',
                    'alt' => 'Latest Instagram stories',
                    'preview_url' => $first['preview_url'] ?? null,
                    'permalink' => null,
                    'opens' => 'viewer',
                    'story_index' => 0,
                    'is_latest' => true,
                ]);
            }

            foreach ($highlights as $highlight) {
                $rings->push([
                    'kind' => 'highlight',
                    'id' => $highlight['id'],
                    'label' => $highlight['title'],
                    'alt' => $highlight['alt'],
                    'preview_url' => $highlight['cover_url'],
                    'permalink' => $highlight['permalink'],
                    'opens' => 'external',
                    'story_index' => null,
                    'is_latest' => false,
                ]);
            }

            return $rings->values();
        }

        foreach ($liveStories->values() as $index => $story) {
            $rings->push([
                'kind' => 'live',
                'id' => $story['id'],
                'label' => $story['label'],
                'alt' => $story['alt'],
                'preview_url' => $story['preview_url'],
                'permalink' => $story['permalink'],
                'opens' => 'viewer',
                'story_index' => $index,
                'is_latest' => $index === 0,
            ]);
        }

        return $rings->values();
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentFeed(InstagramMedia $media): array
    {
        $caption = InstagramCaptionSanitizer::text($media->caption, 140);
        $preview = $media->previewUrl();

        return [
            'id' => $media->id,
            'type' => $media->media_type->value,
            'indicator' => $media->media_type->indicator(),
            'caption' => $caption,
            'alt' => InstagramCaptionSanitizer::alt($media->caption),
            'preview_url' => $preview,
            'permalink' => $media->permalink,
            'published_at' => optional($media->published_at)?->toIso8601String(),
            'like_count' => $media->like_count,
            'comments_count' => $media->comments_count,
            'children' => $media->children->map(fn ($child) => [
                'type' => $child->media_type->value,
                'preview_url' => $child->thumbnail_url ?: $child->media_url,
            ])->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentStory(InstagramMedia $media): array
    {
        $label = $media->published_at
            ? $media->published_at->timezone((string) config('app.timezone'))->format('H:i')
            : 'Story';

        return [
            'id' => $media->id,
            'type' => $media->media_type->value,
            'is_video' => $media->media_type->isVideo(),
            'caption' => InstagramCaptionSanitizer::text($media->caption, 180),
            'alt' => InstagramCaptionSanitizer::alt($media->caption, 'Instagram story from EPIK EPC'),
            'preview_url' => $media->previewUrl(),
            'media_url' => $media->media_url ?: $media->thumbnail_url,
            'permalink' => $media->permalink,
            'label' => $label,
            'published_at' => optional($media->published_at)?->toIso8601String(),
            'duration_ms' => $media->media_type->isVideo()
                ? null
                : (int) config('instagram.image_duration_ms', 5000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentHighlight(InstagramHighlight $highlight): array
    {
        $title = InstagramCaptionSanitizer::text($highlight->title, 40) ?: 'Highlight';

        return [
            'id' => $highlight->id,
            'title' => $title,
            'alt' => 'Instagram highlight: '.$title,
            'cover_url' => $this->publicAssetUrl($highlight->cover_url),
            'permalink' => $highlight->permalink,
            'sort_order' => $highlight->sort_order,
        ];
    }

    protected function publicAssetUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return '/'.ltrim($url, '/');
    }

    /**
     * @param  array<string, mixed>  $fallback
     */
    protected function profileUrl(InstagramSetting $settings, string $fallback): string
    {
        $url = $settings->profile_url;

        if (! $url && $settings->username) {
            $url = 'https://www.instagram.com/'.$settings->username.'/';
        }

        $url = $url ?: $fallback;
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : $fallback;
    }
}
