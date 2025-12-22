<?php

namespace App\Providers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use App\Filesystem\GoogleCloudStorageAdapterWithUrl;

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

            $adapter = new GoogleCloudStorageAdapterWithUrl($bucket);

            $driver = new Filesystem($adapter);

            return new LaravelFilesystemAdapter($driver, $adapter, $config);
        });
    }
}

