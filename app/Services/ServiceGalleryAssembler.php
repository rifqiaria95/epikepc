<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Collection;

/**
 * Builds a flat gallery list from uploaded service images in a single query.
 * Keeps URL resolution out of the Blade layer to avoid repeated Storage hits.
 */
class ServiceGalleryAssembler
{
    public function __construct(
        protected FileStorageService $storage,
    ) {}

    /**
     * @return Collection<int, array{url: string, title: string, label: string, caption: string, alt: string}>
     */
    public function assemble(int $limit = 8): Collection
    {
        $limit = max(1, $limit);

        $services = Service::query()
            ->withoutTrashed()
            ->select(['id', 'title', 'image', 'image_secondary', 'image_tertiary', 'service_type_id'])
            ->with(['serviceType:id,name'])
            ->where(function ($query) {
                $query->whereNotNull('image')
                    ->orWhereNotNull('image_secondary')
                    ->orWhereNotNull('image_tertiary');
            })
            ->orderByDesc('created_at')
            ->get();

        $items = collect();

        foreach ($services as $service) {
            $label = $service->serviceType?->name ?: 'Services';

            foreach (['image', 'image_secondary', 'image_tertiary'] as $field) {
                $path = $service->{$field};

                if (blank($path)) {
                    continue;
                }

                $normalized = ltrim((string) $path, '/');

                if (! $this->storage->fileExists($normalized)) {
                    continue;
                }

                $url = $this->storage->getFileUrl($normalized);

                $items->push([
                    'url' => $url,
                    'title' => $service->title,
                    'label' => $label,
                    'caption' => $service->title,
                    'alt' => $service->title.' — '.$label,
                ]);

                if ($items->count() >= $limit) {
                    return $items->values();
                }
            }
        }

        return $items->values();
    }
}
