<?php

namespace App\Queries\Certificate;

use App\Models\Certificate;
use App\Services\FileStorageService;
use Illuminate\Support\Collection;

class CertificateHomepageQuery
{
    public function __construct(
        protected FileStorageService $storage,
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function publishedItems(): Collection
    {
        $rows = Certificate::query()
            ->visibleOnFrontend()
            ->ordered()
            ->select([
                'id', 'title', 'issuer', 'description',
                'issued_at', 'expires_at', 'credential_url',
                'image_path', 'thumbnail_path', 'image_alt',
                'display_order',
            ])
            ->limit((int) config('certificates.homepage_max_items'))
            ->get();

        return $rows
            ->map(fn (Certificate $cert) => $this->present($cert))
            ->filter(fn (array $item) => ! empty($item['image_url']))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    protected function present(Certificate $cert): array
    {
        $imageUrl = $this->url($cert->image_path);
        $thumbUrl = $cert->thumbnail_path ? $this->url($cert->thumbnail_path) : $imageUrl;

        return [
            'id' => $cert->id,
            'title' => $cert->title,
            'issuer' => $cert->issuer,
            'description' => $cert->description,
            'issued_at' => optional($cert->issued_at)?->format('d M Y'),
            'expires_at' => optional($cert->expires_at)?->format('d M Y'),
            'credential_url' => $this->safeCredentialUrl($cert->credential_url),
            'image_url' => $imageUrl,
            'thumbnail_url' => $thumbUrl,
            'image_alt' => $cert->image_alt,
        ];
    }

    protected function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        try {
            // Certificate uploads are stored on the public disk via FileStorageService.
            return '/storage/'.ltrim($path, '/');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function safeCredentialUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }
}
