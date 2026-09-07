<?php

namespace App\Services\Instagram;

use App\Enums\InstagramErrorClass;
use App\Enums\InstagramMediaType;
use App\Enums\InstagramPostStatus;
use App\Exceptions\Instagram\InstagramApiException;
use App\Models\InstagramPost;
use App\Models\User;
use App\Services\FileStorageService;
use App\Support\Instagram\InstagramCaptionSanitizer;
use App\Support\Instagram\InstagramLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class InstagramPublishService
{
    public function __construct(
        protected InstagramClient $client,
        protected FileStorageService $storage,
        protected InstagramLogger $logger,
        protected InstagramSyncService $sync,
    ) {}

    /**
     * @param  array{media_type: string, caption?: string|null, scheduled_at?: string|null, publish_now?: bool}  $input
     */
    public function create(array $input, UploadedFile $file, ?User $actor = null): InstagramPost
    {
        $mediaType = InstagramMediaType::from(strtoupper((string) $input['media_type']));
        $upload = $this->uploadMedia($file, $mediaType);
        $publishNow = (bool) ($input['publish_now'] ?? false);
        $scheduledAt = $publishNow
            ? now()
            : (isset($input['scheduled_at']) ? Carbon::parse($input['scheduled_at']) : null);

        if (! $publishNow && ($scheduledAt === null || $scheduledAt->lessThanOrEqualTo(now()->addMinutes(1)))) {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Scheduled time must be at least 1 minute in the future.',
            );
        }

        $post = InstagramPost::query()->create([
            'media_type' => $mediaType,
            'caption' => InstagramCaptionSanitizer::text($input['caption'] ?? null, 2200) ?: null,
            'media_path' => $upload['path'],
            'media_url' => $upload['url'],
            'status' => InstagramPostStatus::Scheduled,
            'scheduled_at' => $scheduledAt,
            'created_by' => $actor?->id,
        ]);

        return $post->fresh();
    }

    public function cancel(InstagramPost $post): InstagramPost
    {
        if (! $post->status->isCancellable()) {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Only draft or scheduled posts can be cancelled.',
            );
        }

        $post->forceFill([
            'status' => InstagramPostStatus::Cancelled,
            'last_error_class' => null,
            'last_error_message' => null,
        ])->save();

        return $post->fresh();
    }

    public function publish(InstagramPost $post): InstagramPost
    {
        $locked = DB::transaction(function () use ($post) {
            /** @var InstagramPost $row */
            $row = InstagramPost::query()->lockForUpdate()->findOrFail($post->id);

            if ($row->status === InstagramPostStatus::Published) {
                return $row;
            }

            if (! in_array($row->status, [InstagramPostStatus::Scheduled, InstagramPostStatus::Failed], true)) {
                throw new InstagramApiException(
                    InstagramErrorClass::Malformed,
                    'Post is not ready to publish.',
                );
            }

            $row->forceFill([
                'status' => InstagramPostStatus::Publishing,
                'last_error_class' => null,
                'last_error_message' => null,
            ])->save();

            return $row->fresh();
        });

        try {
            $mediaUrl = $locked->publicMediaUrl();

            if (! $mediaUrl || ! preg_match('#^https://#i', $mediaUrl)) {
                throw new InstagramApiException(
                    InstagramErrorClass::Configuration,
                    'Media URL must be publicly reachable over HTTPS for Instagram to fetch.',
                );
            }

            $container = $locked->isVideo()
                ? $this->client->createVideoContainer($mediaUrl, $locked->caption, $locked->media_type->value)
                : $this->client->createImageContainer($mediaUrl, $locked->caption);

            $containerId = $container['id'];
            $locked->forceFill(['container_id' => $containerId])->save();

            $this->waitUntilFinished($containerId);

            $published = $this->client->publishContainer($containerId);

            $locked->forceFill([
                'status' => InstagramPostStatus::Published,
                'external_media_id' => $published['id'],
                'published_at' => now(),
                'last_error_class' => null,
                'last_error_message' => null,
            ])->save();

            try {
                $this->sync->sync(feed: true, stories: false);
            } catch (Throwable) {
                // Feed sync failure must not roll back a successful publish.
            }

            return $locked->fresh();
        } catch (InstagramApiException $e) {
            $this->logger->exception($e, 'publish');
            $locked->forceFill([
                'status' => InstagramPostStatus::Failed,
                'last_error_class' => $e->errorClass->value,
                'last_error_message' => $e->safeMessage(),
            ])->save();

            throw $e;
        } catch (Throwable $e) {
            $wrapped = new InstagramApiException(
                InstagramErrorClass::Transient,
                'Instagram publish failed.',
                previous: $e,
            );
            $this->logger->exception($wrapped, 'publish');
            $locked->forceFill([
                'status' => InstagramPostStatus::Failed,
                'last_error_class' => InstagramErrorClass::Transient->value,
                'last_error_message' => $wrapped->safeMessage(),
            ])->save();

            throw $wrapped;
        }
    }

    /**
     * @return array{path: string, url: string}
     */
    protected function uploadMedia(UploadedFile $file, InstagramMediaType $type): array
    {
        $directory = (string) config('instagram.upload_directory', 'instagram/posts');

        if ($type === InstagramMediaType::Image) {
            $result = $this->storage->uploadImage($file, $directory);
        } else {
            $allowed = config('instagram.publish_video_mimes', ['video/mp4', 'video/quicktime']);
            if (! in_array($file->getMimeType(), $allowed, true)) {
                throw new InstagramApiException(
                    InstagramErrorClass::Malformed,
                    'Video must be MP4 or MOV.',
                );
            }
            $result = $this->storage->uploadFile($file, $directory);
        }

        if (! ($result['success'] ?? false) || empty($result['path'])) {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                $result['error'] ?? 'Unable to upload media.',
            );
        }

        $url = $result['url'] ?? $this->storage->getFileUrl($result['path']);
        if (! preg_match('#^https?://#i', (string) $url)) {
            $url = url($url);
        }

        return [
            'path' => (string) $result['path'],
            'url' => (string) $url,
        ];
    }

    protected function waitUntilFinished(string $containerId): void
    {
        $attempts = max(1, (int) config('instagram.publish_poll_attempts', 20));
        $sleepMs = max(200, (int) config('instagram.publish_poll_sleep_ms', 1500));

        for ($i = 1; $i <= $attempts; $i++) {
            $status = $this->client->containerStatus($containerId);
            $code = strtoupper((string) ($status['status_code'] ?? $status['status'] ?? ''));

            if (in_array($code, ['FINISHED', 'PUBLISHED'], true)) {
                return;
            }

            if (in_array($code, ['ERROR', 'EXPIRED'], true)) {
                throw new InstagramApiException(
                    InstagramErrorClass::Malformed,
                    'Instagram media container failed processing.',
                );
            }

            usleep($sleepMs * 1000);
        }

        throw new InstagramApiException(
            InstagramErrorClass::Timeout,
            'Instagram media container did not finish processing in time.',
        );
    }
}
