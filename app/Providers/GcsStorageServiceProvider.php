<?php

namespace App\Providers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use Illuminate\Support\Facades\Storage;

class GcsStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('gcs', function ($app, $config) {
            // Convert relative path to absolute path
            $keyFilePath = $config['key_file'];
            if (!file_exists($keyFilePath)) {
                $keyFilePath = base_path($config['key_file']);
            }

            $client = new StorageClient([
                'projectId' => $config['project_id'],
                'keyFilePath' => $keyFilePath, // path ke service account json
            ]);

            $bucket = $client->bucket($config['bucket']);

            $adapter = new GoogleCloudStorageAdapter($bucket);

            return new Filesystem($adapter);
        });
    }
}

