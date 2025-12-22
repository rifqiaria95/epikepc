<?php

namespace App\Filesystem;

use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

class GoogleCloudStorageAdapterWithUrl extends GoogleCloudStorageAdapter
{
    /**
     * Generate a public URL for the given path based on configured base URL.
     */
    public function getUrl(string $path): string
    {
        $baseUrl = (string) (config('filesystems.disks.gcs.url') ?? '');

        if ($baseUrl === '') {
            return $path;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
    }
}


