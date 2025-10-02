<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GCS Debug Production ===\n";

try {
    // Test 1: Check environment
    echo "1. Environment Check:\n";
    echo "   Environment: " . app()->environment() . "\n";
    echo "   FILESYSTEM_DISK: " . env('FILESYSTEM_DISK') . "\n";
    echo "   GOOGLE_CLOUD_PROJECT: " . env('GOOGLE_CLOUD_PROJECT') . "\n";
    echo "   GOOGLE_CLOUD_BUCKET: " . env('GOOGLE_CLOUD_BUCKET') . "\n";
    echo "   GOOGLE_CLOUD_KEY_FILE: " . env('GOOGLE_CLOUD_KEY_FILE') . "\n";
    
    // Test 2: Check key file
    echo "\n2. Key File Check:\n";
    $keyFile = env('GOOGLE_CLOUD_KEY_FILE');
    if (file_exists($keyFile)) {
        echo "   ✅ Key file exists: " . $keyFile . "\n";
    } else {
        echo "   ❌ Key file not found: " . $keyFile . "\n";
        $keyFileWithBasePath = base_path($keyFile);
        if (file_exists($keyFileWithBasePath)) {
            echo "   ✅ Key file exists with base_path: " . $keyFileWithBasePath . "\n";
        } else {
            echo "   ❌ Key file not found with base_path: " . $keyFileWithBasePath . "\n";
        }
    }
    
    // Test 3: Check config
    echo "\n3. Config Check:\n";
    echo "   config('filesystems.disks.gcs.project_id'): " . config('filesystems.disks.gcs.project_id') . "\n";
    echo "   config('filesystems.disks.gcs.key_file'): " . config('filesystems.disks.gcs.key_file') . "\n";
    echo "   config('filesystems.disks.gcs.bucket'): " . config('filesystems.disks.gcs.bucket') . "\n";
    echo "   config('filesystems.disks.gcs.url'): " . config('filesystems.disks.gcs.url') . "\n";
    
    // Test 4: Test FileStorageService
    echo "\n4. FileStorageService Test:\n";
    $service = new \App\Services\FileStorageService();
    
    // Create test file
    file_put_contents(storage_path('app/debug-test.txt'), 'Debug test content');
    
    // Create UploadedFile
    $file = new \Illuminate\Http\UploadedFile(
        storage_path('app/debug-test.txt'),
        'debug-test.txt',
        'text/plain',
        null,
        true
    );
    
    echo "   Testing upload...\n";
    $result = $service->uploadFile($file, 'debug');
    
    echo "   Result:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
    // Cleanup
    unlink(storage_path('app/debug-test.txt'));
    
    // Test 5: Check logs
    echo "\n5. Recent Logs:\n";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $lines = explode("\n", $logs);
        $recentLines = array_slice($lines, -20);
        foreach ($recentLines as $line) {
            if (strpos($line, 'FileStorageService') !== false || strpos($line, 'GCS') !== false) {
                echo "   " . $line . "\n";
            }
        }
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
