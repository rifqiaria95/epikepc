<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Public storage upload test ===\n";

try {
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    echo '1. Disk URL: '.config('filesystems.disks.public.url')."\n";

    $path = 'test-uploads/test-'.time().'.txt';
    $disk->put($path, 'Public storage test — '.date('c'));
    echo "2. Write OK: {$path}\n";
    echo '   URL: '.$disk->url($path)."\n";

    if ($disk->exists($path)) {
        echo "3. exists(): yes\n";
    }

    $disk->delete($path);
    echo "4. Cleaned up test object.\n";

    $tmp = tempnam(sys_get_temp_dir(), 'up');
    file_put_contents($tmp, 'FileStorageService probe');
    $uploadedFile = new \Illuminate\Http\UploadedFile($tmp, 'probe.txt', 'text/plain', null, true);
    $svc = app(\App\Services\FileStorageService::class);
    $result = $svc->uploadFile($uploadedFile, 'test');
    if ($result['success']) {
        echo "5. FileStorageService OK — {$result['path']}\n";
        $svc->deleteFile($result['path']);
        echo "   (test file removed)\n";
    } else {
        echo '5. FileStorageService failed: '.($result['error'] ?? 'unknown')."\n";
    }

    echo "\nDone.\n";
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    exit(1);
}
