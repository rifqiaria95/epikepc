<?php

namespace App\Http\Controllers\Mono;

use App\Enums\InstagramPostStatus;
use App\Exceptions\Instagram\InstagramApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instagram\InstagramHighlightRequest;
use App\Http\Requests\Instagram\InstagramPostStoreRequest;
use App\Http\Requests\Instagram\InstagramReorderRequest;
use App\Http\Requests\Instagram\InstagramSettingsRequest;
use App\Http\Requests\Instagram\InstagramSyncRequest;
use App\Http\Requests\Instagram\InstagramVisibilityRequest;
use App\Jobs\PublishInstagramPostJob;
use App\Jobs\SyncInstagramJob;
use App\Models\InstagramHighlight;
use App\Models\InstagramMedia;
use App\Models\InstagramPost;
use App\Models\InstagramSetting;
use App\Queries\Internal\InternalSummaryQuery;
use App\Services\FileStorageService;
use App\Services\Instagram\InstagramFeedService;
use App\Services\Instagram\InstagramPublishService;
use App\Services\Instagram\InstagramSyncService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstagramController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected InstagramFeedService $feed,
        protected InstagramSyncService $sync,
        protected InstagramPublishService $publisher,
        protected InternalSummaryQuery $summary,
        protected FileStorageService $storage,
    ) {}

    public function index(): View
    {
        $settings = InstagramSetting::current();
        $this->authorize('view', $settings);

        $media = InstagramMedia::query()
            ->feed()
            ->with(['children:id,instagram_media_id,media_type'])
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        $highlights = InstagramHighlight::query()
            ->ordered()
            ->limit(50)
            ->get();

        $posts = InstagramPost::query()
            ->with('creator:id,name')
            ->latest()
            ->limit(50)
            ->get();

        return view('internal.instagram.index', [
            'settings' => $settings,
            'stats' => $this->summary->cards('instagram'),
            'media' => $media,
            'highlights' => $highlights,
            'posts' => $posts,
            'widget' => $this->feed->buildWidget(),
            'configEnabled' => (bool) config('instagram.enabled'),
            'configured' => $this->isConfigured(),
        ]);
    }

    public function updateSettings(InstagramSettingsRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        $data = $request->validated();
        $data['profile_url'] = $this->safeUrl($data['profile_url'] ?? null);

        $settings->fill($data)->save();
        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => 'Instagram section settings saved.',
        ]);
    }

    public function toggle(string $id, InstagramVisibilityRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        $media = InstagramMedia::query()->feed()->findOrFail($id);
        $media->is_visible = (bool) $request->boolean('is_visible');
        $media->save();
        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => $media->is_visible ? 'Feed item is now visible.' : 'Feed item is now hidden.',
        ]);
    }

    public function reorder(InstagramReorderRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        $ids = array_values($request->validated('ordered_ids'));

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                InstagramMedia::query()->feed()->where('id', $id)->update(['sort_order' => $index]);
            }
        });

        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => 'Feed order updated.',
        ]);
    }

    public function storeHighlight(InstagramHighlightRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        $data = $this->highlightPayload($request);
        $data['sort_order'] = $data['sort_order']
            ?? ((int) InstagramHighlight::query()->max('sort_order') + 1);

        InstagramHighlight::query()->create($data);
        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => 'Highlight saved.',
        ]);
    }

    public function updateHighlight(string $id, InstagramHighlightRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        $highlight = InstagramHighlight::query()->findOrFail($id);
        $highlight->fill($this->highlightPayload($request, $highlight))->save();
        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => 'Highlight updated.',
        ]);
    }

    public function destroyHighlight(string $id): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        InstagramHighlight::query()->findOrFail($id)->delete();
        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => 'Highlight deleted.',
        ]);
    }

    public function toggleHighlight(string $id, InstagramVisibilityRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('update', $settings);

        $highlight = InstagramHighlight::query()->findOrFail($id);
        $highlight->is_visible = (bool) $request->boolean('is_visible');
        $highlight->save();
        $this->sync->forgetPublicCache();

        return response()->json([
            'status' => 200,
            'message' => $highlight->is_visible ? 'Highlight is now visible.' : 'Highlight is now hidden.',
        ]);
    }

    public function storePost(InstagramPostStoreRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('publish', $settings);

        try {
            $mediaType = strtoupper((string) $request->validated('media_type'));
            if ($mediaType === 'VIDEO') {
                $mediaType = 'REELS';
            }

            $post = $this->publisher->create([
                'media_type' => $mediaType,
                'caption' => $request->validated('caption'),
                'scheduled_at' => $request->validated('scheduled_at'),
                'publish_now' => $request->boolean('publish_now'),
            ], $request->file('media'), $request->user());

            $shouldDispatch = $request->boolean('publish_now')
                || ($post->scheduled_at && $post->scheduled_at->lessThanOrEqualTo(now()));

            if ($shouldDispatch) {
                if (config('queue.default') === 'sync') {
                    $this->publisher->publish($post->fresh());
                } else {
                    PublishInstagramPostJob::dispatch($post->id);
                }
            }

            return response()->json([
                'status' => 200,
                'message' => $request->boolean('publish_now')
                    ? 'Instagram post submitted for publishing.'
                    : 'Instagram post scheduled.',
                'post_id' => $post->id,
            ]);
        } catch (InstagramApiException $e) {
            return response()->json([
                'status' => 422,
                'message' => $e->safeMessage(),
            ], 422);
        }
    }

    public function cancelPost(string $id): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('publish', $settings);

        try {
            $post = InstagramPost::query()->findOrFail($id);
            $this->publisher->cancel($post);

            return response()->json([
                'status' => 200,
                'message' => 'Scheduled post cancelled.',
            ]);
        } catch (InstagramApiException $e) {
            return response()->json([
                'status' => 422,
                'message' => $e->safeMessage(),
            ], 422);
        }
    }

    public function retryPost(string $id): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('publish', $settings);

        $post = InstagramPost::query()->findOrFail($id);

        if (! in_array($post->status->value, ['scheduled', 'failed'], true)) {
            return response()->json([
                'status' => 422,
                'message' => 'Only scheduled or failed posts can be published now.',
            ], 422);
        }

        $post->forceFill([
            'status' => InstagramPostStatus::Scheduled,
            'scheduled_at' => now(),
            'last_error_class' => null,
            'last_error_message' => null,
        ])->save();

        if (config('queue.default') === 'sync') {
            try {
                $this->publisher->publish($post->fresh());
            } catch (InstagramApiException $e) {
                return response()->json([
                    'status' => 422,
                    'message' => $e->safeMessage(),
                ], 422);
            }
        } else {
            PublishInstagramPostJob::dispatch($post->id);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Publish job started.',
        ]);
    }

    public function sync(InstagramSyncRequest $request): JsonResponse
    {
        $settings = InstagramSetting::current();
        $this->authorize('sync', $settings);

        $scope = $request->validated('scope') ?? 'all';
        $feed = $scope !== 'stories';
        $stories = $scope !== 'feed';

        if (config('queue.default') === 'sync') {
            $result = $this->sync->sync(feed: $feed, stories: $stories);

            return response()->json([
                'status' => $result['errors'] === [] ? 200 : 422,
                'message' => $result['errors'] === []
                    ? 'Instagram sync completed.'
                    : 'Instagram sync finished with errors.',
                'result' => $this->publicResult($result),
            ], $result['errors'] === [] ? 200 : 422);
        }

        SyncInstagramJob::dispatch($feed, $stories);

        return response()->json([
            'status' => 202,
            'message' => 'Instagram sync has been queued.',
        ], 202);
    }

    /**
     * @return array<string, mixed>
     */
    protected function highlightPayload(InstagramHighlightRequest $request, ?InstagramHighlight $existing = null): array
    {
        $data = $request->safe()->only(['title', 'cover_url', 'permalink', 'is_visible', 'sort_order']);

        if ($request->hasFile('cover')) {
            $upload = $this->storeCover($request->file('cover'));
            $data['cover_url'] = $upload;
        }

        $data['cover_url'] = $this->normalizeCoverUrl((string) ($data['cover_url'] ?? $existing?->cover_url ?? ''));
        $data['permalink'] = $this->safeUrl((string) ($data['permalink'] ?? '')) ?? ($existing?->permalink ?? '');
        $data['is_visible'] = array_key_exists('is_visible', $data)
            ? (bool) $data['is_visible']
            : ($existing?->is_visible ?? true);

        return $data;
    }

    protected function storeCover(UploadedFile $file): string
    {
        $result = $this->storage->uploadImage($file, 'instagram/highlights');

        if (! ($result['success'] ?? false) || empty($result['path'])) {
            abort(422, $result['error'] ?? 'Unable to upload highlight cover.');
        }

        return $this->storage->getFileUrl($result['path']);
    }

    protected function normalizeCoverUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            abort(422, 'Cover image URL is required.');
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            abort(422, 'Cover image URL must be http(s) or a site-relative path.');
        }

        return $url;
    }

    /**
     * @param  array{feed: bool, stories: bool, profile: bool, errors: array<string, string>}  $result
     * @return array<string, mixed>
     */
    protected function publicResult(array $result): array
    {
        return [
            'feed' => $result['feed'],
            'stories' => $result['stories'],
            'profile' => $result['profile'],
            'errors' => $result['errors'],
        ];
    }

    protected function isConfigured(): bool
    {
        return trim((string) config('instagram.user_id')) !== ''
            && trim((string) config('instagram.access_token')) !== '';
    }

    protected function safeUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }
}
