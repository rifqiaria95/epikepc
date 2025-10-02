<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GCS Upload Test ===\n";

try {
    // Test 1: Check configuration
    echo "1. Checking GCS configuration...\n";
    $projectId = config('filesystems.disks.gcs.project_id');
    $keyFile = config('filesystems.disks.gcs.key_file');
    $bucketName = config('filesystems.disks.gcs.bucket');
    $url = config('filesystems.disks.gcs.url');
    
    echo "   Project ID: " . $projectId . "\n";
    echo "   Key File: " . $keyFile . "\n";
    echo "   Bucket: " . $bucketName . "\n";
    echo "   URL: " . $url . "\n";
    
    // Test 2: Check key file exists
    echo "\n2. Checking key file...\n";
    if (file_exists($keyFile)) {
        echo "   ✅ Key file exists\n";
        $keyContent = json_decode(file_get_contents($keyFile), true);
        if ($keyContent && isset($keyContent['project_id'])) {
            echo "   ✅ Key file is valid JSON\n";
            echo "   Project ID in key: " . $keyContent['project_id'] . "\n";
        } else {
            echo "   ❌ Key file is not valid JSON\n";
        }
    } else {
        echo "   ❌ Key file does not exist\n";
        // Try with base_path
        $keyFileWithBasePath = base_path($keyFile);
        if (file_exists($keyFileWithBasePath)) {
            echo "   ✅ Key file exists with base_path: " . $keyFileWithBasePath . "\n";
        } else {
            echo "   ❌ Key file does not exist with base_path either\n";
        }
    }
    
    // Test 3: Test GCS connection
    echo "\n3. Testing GCS connection...\n";
    $client = new \Google\Cloud\Storage\StorageClient([
        'projectId' => $projectId,
        'keyFilePath' => file_exists($keyFile) ? $keyFile : base_path($keyFile)
    ]);
    
    $bucket = $client->bucket($bucketName);
    echo "   ✅ GCS client created successfully\n";
    echo "   ✅ Bucket object created: " . $bucket->name() . "\n";
    
    // Test 4: Test file upload
    echo "\n4. Testing file upload...\n";
    $testContent = "Test file content - " . date('Y-m-d H:i:s');
    $testPath = 'test-uploads/test-file-' . time() . '.txt';
    
    $object = $bucket->upload($testContent, [
        'name' => $testPath,
        'metadata' => [
            'contentType' => 'text/plain',
        ],
    ]);
    
    echo "   ✅ File uploaded successfully\n";
    echo "   File path: " . $testPath . "\n";
    echo "   Public URL: " . $url . '/' . $testPath . "\n";
    
    // Test 5: Test FileStorageService
    echo "\n5. Testing FileStorageService...\n";
    $fileStorageService = new \App\Services\FileStorageService();
    
    // Create a test uploaded file
    $testFile = new \Illuminate\Http\UploadedFile(
        storage_path('app/test.txt'),
        'test.txt',
        'text/plain',
        null,
        true
    );
    
    // Create test file
    file_put_contents(storage_path('app/test.txt'), 'Test content for FileStorageService');
    
    $result = $fileStorageService->uploadFile($testFile, 'test');
    
    if ($result['success']) {
        echo "   ✅ FileStorageService upload successful\n";
        echo "   Filename: " . $result['filename'] . "\n";
        echo "   Path: " . $result['path'] . "\n";
        echo "   URL: " . $result['url'] . "\n";
    } else {
        echo "   ❌ FileStorageService upload failed\n";
        echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
    
    // Cleanup
    unlink(storage_path('app/test.txt'));
    
    echo "\n=== All tests completed successfully! ===\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
