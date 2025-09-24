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
        Storage::extend('aws-s3', function ($app, $config) {
            $client = new StorageClient([
                'projectId' => $config['project_id'],
                'keyFilePath' => $config['key_file'], // path ke service account json
            ]);

            $bucket = $client->bucket($config['bucket']);

            $adapter = new GoogleCloudStorageAdapter($bucket);

            return new Filesystem($adapter);
        });
    }
}

