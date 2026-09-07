<?php

namespace App\Services\Instagram;

use App\Enums\InstagramErrorClass;
use App\Exceptions\Instagram\InstagramApiException;
use App\Support\Instagram\InstagramErrorClassifier;
use App\Support\Instagram\InstagramResponseNormalizer;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

class InstagramClient
{
    public function __construct(
        protected InstagramResponseNormalizer $normalizer,
        protected InstagramErrorClassifier $classifier,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function profile(): array
    {
        $payload = $this->get($this->userId(), [
            'fields' => 'id,username,account_type,profile_picture_url',
        ]);

        return $this->normalizer->profile($payload);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function feed(int $limit): array
    {
        $fields = 'id,caption,media_type,media_product_type,media_url,permalink,thumbnail_url,timestamp,children{id,media_type,media_url,thumbnail_url}';

        if (config('instagram.include_engagement')) {
            $fields .= ',like_count,comments_count';
        }

        $payload = $this->get($this->userId().'/media', [
            'fields' => $fields,
            'limit' => max(1, min($limit, 25)),
        ]);

        return $this->collection($payload, false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function stories(int $limit): array
    {
        $payload = $this->get($this->userId().'/stories', [
            'fields' => 'id,caption,media_type,media_product_type,media_url,permalink,thumbnail_url,timestamp',
            'limit' => max(1, min($limit, 25)),
        ]);

        return $this->collection($payload, true);
    }

    /**
     * Create an IMAGE media container for Content Publishing.
     *
     * @return array{id: string}
     */
    public function createImageContainer(string $imageUrl, ?string $caption = null): array
    {
        $payload = [
            'image_url' => $imageUrl,
        ];

        if ($caption !== null && $caption !== '') {
            $payload['caption'] = $caption;
        }

        return $this->requireId($this->post($this->userId().'/media', $payload));
    }

    /**
     * Create a VIDEO/REELS media container for Content Publishing.
     *
     * @return array{id: string}
     */
    public function createVideoContainer(string $videoUrl, ?string $caption = null, string $mediaType = 'REELS'): array
    {
        $type = strtoupper($mediaType);
        if (! in_array($type, ['VIDEO', 'REELS'], true)) {
            $type = 'REELS';
        }

        $payload = [
            'video_url' => $videoUrl,
            'media_type' => $type,
        ];

        if ($caption !== null && $caption !== '') {
            $payload['caption'] = $caption;
        }

        return $this->requireId($this->post($this->userId().'/media', $payload));
    }

    /**
     * @return array{status_code?: string, status?: string, id?: string}
     */
    public function containerStatus(string $containerId): array
    {
        $id = $this->assertGraphId($containerId);

        return $this->get($id, [
            'fields' => 'id,status_code,status',
        ]);
    }

    /**
     * @return array{id: string}
     */
    public function publishContainer(string $containerId): array
    {
        return $this->requireId($this->post($this->userId().'/media_publish', [
            'creation_id' => $this->assertGraphId($containerId),
        ]));
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function get(string $path, array $query = []): array
    {
        return $this->send('get', $path, $query);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    protected function post(string $path, array $body = []): array
    {
        return $this->send('post', $path, $body);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function send(string $method, string $path, array $payload = []): array
    {
        $this->assertConfigured();

        $attempts = max(1, (int) config('instagram.http_retries', 2) + 1);
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $pending = $this->http();
                $response = $method === 'post'
                    ? $pending->asForm()->post($this->endpoint($path), $payload)
                    : $pending->get($this->endpoint($path), $payload);

                if ($response->successful()) {
                    $json = $response->json();

                    if (! is_array($json)) {
                        throw new InstagramApiException(
                            InstagramErrorClass::Malformed,
                            'Instagram API returned a non-object payload.',
                            $response->status(),
                        );
                    }

                    return $json;
                }

                $exception = $this->classifier->fromResponse($response);

                if (! $exception->retryable() || $attempt === $attempts) {
                    throw $exception;
                }

                $lastException = $exception;
            } catch (Throwable $e) {
                $exception = $this->classifier->fromThrowable($e);

                if (! $exception->retryable() || $attempt === $attempts) {
                    throw $exception;
                }

                $lastException = $exception;
            }

            usleep((int) (250000 * (2 ** ($attempt - 1))));
        }

        throw $lastException ?? new InstagramApiException(
            InstagramErrorClass::Transient,
            'Instagram API request failed after retries.',
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{id: string}
     */
    protected function requireId(array $payload): array
    {
        $id = trim((string) ($payload['id'] ?? ''));

        if ($id === '') {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Instagram API response is missing an id.',
            );
        }

        return ['id' => $id];
    }

    protected function assertGraphId(string $id): string
    {
        $id = trim($id);

        if ($id === '' || ! preg_match('/^[A-Za-z0-9._-]+$/', $id)) {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Instagram container id is invalid.',
            );
        }

        return $id;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    protected function collection(array $payload, bool $isStory): array
    {
        $rows = data_get($payload, 'data', []);

        if (! is_array($rows)) {
            throw new InstagramApiException(
                InstagramErrorClass::Malformed,
                'Instagram API collection is not a list.',
            );
        }

        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $normalized[] = $this->normalizer->media($row, $isStory);
        }

        return $normalized;
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->timeout((int) config('instagram.http_timeout', 10))
            ->withToken($this->token())
            ->withOptions(['http_errors' => false]);
    }

    protected function endpoint(string $path): string
    {
        $version = trim((string) config('instagram.api_version'), '/');
        $path = ltrim($path, '/');

        return $version !== '' ? $version.'/'.$path : $path;
    }

    protected function baseUrl(): string
    {
        $base = rtrim((string) config('instagram.api_base_url'), '/');
        $host = parse_url($base, PHP_URL_HOST);
        $allowed = config('instagram.allowed_hosts', []);

        if (! is_string($host) || ! in_array($host, $allowed, true)) {
            throw new InstagramApiException(
                InstagramErrorClass::Configuration,
                'Instagram API base URL is not allowed.',
            );
        }

        return $base;
    }

    protected function userId(): string
    {
        $userId = trim((string) config('instagram.user_id'));

        if ($userId === '' || ! preg_match('/^[A-Za-z0-9._-]+$/', $userId)) {
            throw new InstagramApiException(
                InstagramErrorClass::Configuration,
                'Instagram user id is not configured.',
            );
        }

        return $userId;
    }

    protected function token(): string
    {
        $token = trim((string) config('instagram.access_token'));

        if ($token === '') {
            throw new InstagramApiException(
                InstagramErrorClass::Configuration,
                'Instagram access token is not configured.',
            );
        }

        return $token;
    }

    protected function assertConfigured(): void
    {
        if (! config('instagram.enabled')) {
            throw new InstagramApiException(
                InstagramErrorClass::Disabled,
                'Instagram integration is disabled.',
            );
        }

        $this->token();
        $this->userId();
        $this->baseUrl();
    }
}
