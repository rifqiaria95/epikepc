<?php

namespace App\Services\Instagram;

use App\Enums\InstagramErrorClass;
use App\Exceptions\Instagram\InstagramApiException;
use App\Models\InstagramMedia;
use App\Models\InstagramMediaChild;
use App\Models\InstagramSetting;
use App\Support\Instagram\InstagramLogger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InstagramSyncService
{
    public const CACHE_FEED = 'instagram.public.feed';

    public const CACHE_STORIES = 'instagram.public.stories';

    public const CACHE_PROFILE = 'instagram.public.profile';

    public const CACHE_WIDGET = 'instagram.public.widget';

    public function __construct(
        protected InstagramClient $client,
        protected InstagramLogger $logger,
    ) {}

    /**
     * @return array{feed: bool, stories: bool, profile: bool, errors: array<string, string>}
     */
    public function sync(bool $feed = true, bool $stories = true): array
    {
        $lock = Cache::lock('instagram-sync', (int) config('instagram.sync_lock_seconds', 120));

        if (! $lock->get()) {
            return [
                'feed' => false,
                'stories' => false,
                'profile' => false,
                'errors' => ['lock' => 'A sync is already running.'],
            ];
        }

        try {
            return $this->run($feed, $stories);
        } finally {
            $lock->release();
        }
    }

    /**
     * @return array{feed: bool, stories: bool, profile: bool, errors: array<string, string>}
     */
    protected function run(bool $feed, bool $stories): array
    {
        $result = [
            'feed' => false,
            'stories' => false,
            'profile' => false,
            'errors' => [],
        ];

        $settings = InstagramSetting::current();

        if ($feed || $stories) {
            try {
                $profile = $this->client->profile();
                $this->persistProfile($settings, $profile);
                $result['profile'] = true;
            } catch (InstagramApiException $e) {
                $this->fail($settings, $e, 'profile');
                $result['errors']['profile'] = $e->safeMessage();

                if ($e->errorClass === InstagramErrorClass::Authentication
                    || $e->errorClass === InstagramErrorClass::Permission
                    || $e->errorClass === InstagramErrorClass::Configuration
                    || $e->errorClass === InstagramErrorClass::Disabled) {
                    return $result;
                }
            }
        }

        if ($feed) {
            try {
                $items = $this->client->feed((int) config('instagram.feed_limit', 6));
                $this->persistMedia($items, isStory: false);
                $settings->forceFill([
                    'last_successful_feed_sync_at' => now(),
                    'api_status' => 'connected',
                    'token_status' => 'valid',
                    'last_error_class' => null,
                    'last_error_message' => null,
                ])->save();
                $result['feed'] = true;
            } catch (InstagramApiException $e) {
                $this->fail($settings, $e, 'feed');
                $result['errors']['feed'] = $e->safeMessage();
            }
        }

        if ($stories) {
            try {
                $items = $this->client->stories((int) config('instagram.story_limit', 10));
                $this->persistMedia($items, isStory: true);
                $this->expireMissingStories(array_column($items, 'external_media_id'));
                $settings->forceFill([
                    'last_successful_story_sync_at' => now(),
                    'api_status' => 'connected',
                    'token_status' => 'valid',
                    'last_error_class' => null,
                    'last_error_message' => null,
                ])->save();
                $result['stories'] = true;
            } catch (InstagramApiException $e) {
                $this->fail($settings, $e, 'stories');
                $result['errors']['stories'] = $e->safeMessage();
            }
        }

        $this->forgetPublicCache();

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function persistMedia(array $items, bool $isStory): void
    {
        if ($items === []) {
            return;
        }

        $now = now();
        $rows = [];
        $childrenByParent = [];

        foreach ($items as $index => $item) {
            $externalId = (string) $item['external_media_id'];
            $rows[] = [
                'external_media_id' => $externalId,
                'media_type' => $item['media_type'],
                'caption' => $item['caption'],
                'media_url' => $item['media_url'],
                'thumbnail_url' => $item['thumbnail_url'],
                'permalink' => $item['permalink'],
                'published_at' => optional($item['published_at'])?->format('Y-m-d H:i:s'),
                'expires_at' => optional($item['expires_at'])?->format('Y-m-d H:i:s'),
                'is_story' => $isStory ? 1 : 0,
                'is_visible' => 1,
                'sort_order' => $index,
                'like_count' => $item['like_count'],
                'comments_count' => $item['comments_count'],
                'synced_at' => $now->format('Y-m-d H:i:s'),
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ];
            $childrenByParent[$externalId] = $item['children'] ?? [];
        }

        InstagramMedia::upsert(
            $rows,
            ['external_media_id'],
            [
                'media_type',
                'caption',
                'media_url',
                'thumbnail_url',
                'permalink',
                'published_at',
                'expires_at',
                'is_story',
                'like_count',
                'comments_count',
                'synced_at',
                'updated_at',
            ]
        );

        $parents = InstagramMedia::query()
            ->whereIn('external_media_id', array_keys($childrenByParent))
            ->get(['id', 'external_media_id']);

        $this->replaceChildren($parents, $childrenByParent);
    }

    /**
     * @param  Collection<int, InstagramMedia>  $parents
     * @param  array<string, array<int, array<string, mixed>>>  $childrenByParent
     */
    protected function replaceChildren($parents, array $childrenByParent): void
    {
        $parentIds = $parents->pluck('id')->all();

        if ($parentIds === []) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($parents as $parent) {
            foreach ($childrenByParent[$parent->external_media_id] ?? [] as $child) {
                $rows[] = [
                    'instagram_media_id' => $parent->id,
                    'external_media_id' => $child['external_media_id'],
                    'media_type' => $child['media_type'],
                    'media_url' => $child['media_url'],
                    'thumbnail_url' => $child['thumbnail_url'],
                    'sort_order' => $child['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(function () use ($parentIds, $rows) {
            InstagramMediaChild::query()->whereIn('instagram_media_id', $parentIds)->delete();

            if ($rows !== []) {
                InstagramMediaChild::query()->insert($rows);
            }
        });
    }

    /**
     * @param  array<int, string>  $currentIds
     */
    protected function expireMissingStories(array $currentIds): void
    {
        $query = InstagramMedia::query()->stories();

        if ($currentIds !== []) {
            $query->whereNotIn('external_media_id', $currentIds);
        }

        $query->update([
            'expires_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    protected function persistProfile(InstagramSetting $settings, array $profile): void
    {
        $settings->forceFill([
            'username' => $profile['username'],
            'profile_picture_url' => $profile['profile_picture_url'],
            'account_id' => $profile['account_id'],
            'token_status' => 'valid',
            'api_status' => 'connected',
        ])->save();
    }

    protected function fail(InstagramSetting $settings, InstagramApiException $exception, string $operation): void
    {
        $this->logger->exception($exception, $operation);

        $tokenStatus = $settings->token_status ?: 'unknown';
        if ($exception->errorClass === InstagramErrorClass::Authentication) {
            $tokenStatus = 'invalid';
        } elseif ($exception->errorClass === InstagramErrorClass::Configuration) {
            $tokenStatus = 'missing';
        }

        $settings->forceFill([
            'last_failed_sync_at' => now(),
            'last_error_class' => $exception->errorClass->value,
            'last_error_message' => $exception->safeMessage(),
            'token_status' => $tokenStatus,
            'api_status' => $exception->errorClass === InstagramErrorClass::Disabled ? 'disabled' : 'error',
        ])->save();
    }

    public function forgetPublicCache(): void
    {
        Cache::forget(self::CACHE_FEED);
        Cache::forget(self::CACHE_STORIES);
        Cache::forget(self::CACHE_PROFILE);
        Cache::forget(self::CACHE_WIDGET);
    }
}
