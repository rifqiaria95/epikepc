<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Thumbnail Debug Production ===\n";

try {
    // Test 1: Check GCS configuration
    echo "1. GCS Configuration:\n";
    echo "   URL: " . config('filesystems.disks.gcs.url') . "\n";
    echo "   Bucket: " . config('filesystems.disks.gcs.bucket') . "\n";
    echo "   Project: " . config('filesystems.disks.gcs.project_id') . "\n";
    
    // Test 2: Check About model data
    echo "\n2. About Model Data:\n";
    $about = \App\Models\About::first();
    if ($about && $about->image) {
        echo "   Image path: " . $about->image . "\n";
        echo "   Generated URL: " . config('filesystems.disks.gcs.url') . '/' . $about->image . "\n";
    } else {
        echo "   No about data found or no image\n";
    }
    
    // Test 2b: Check specific image path from database
    echo "\n2b. Specific Image Path Test:\n";
    $specificPath = 'uploads/2025/10/about/images/QuhFvLIQx5cIg97CVYCpsj8TBAgS1PJ24S0Vxoww.JPG';
    echo "   Testing path: " . $specificPath . "\n";
    echo "   Generated URL: " . config('filesystems.disks.gcs.url') . '/' . $specificPath . "\n";
    
    // Test 3: Test URL accessibility
    echo "\n3. URL Accessibility Test:\n";
    $testPath = $about && $about->image ? $about->image : $specificPath;
    if ($testPath) {
        $imageUrl = config('filesystems.disks.gcs.url') . '/' . $testPath;
        echo "   Testing URL: " . $imageUrl . "\n";
        
        // Test with curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "   HTTP Status: " . $httpCode . "\n";
        if ($httpCode == 200) {
            echo "   ✅ Image URL is accessible\n";
        } else {
            echo "   ❌ Image URL is not accessible\n";
        }
    }
    
    // Test 4: Check GCS files
    echo "\n4. GCS Files Check:\n";
    try {
        $client = new \Google\Cloud\Storage\StorageClient([
            'projectId' => config('filesystems.disks.gcs.project_id'),
            'keyFilePath' => config('filesystems.disks.gcs.key_file')
        ]);
        $bucket = $client->bucket(config('filesystems.disks.gcs.bucket'));
        
        $objects = $bucket->objects(['prefix' => 'uploads/']);
        $count = 0;
        foreach ($objects as $object) {
            echo "   File: " . $object->name() . "\n";
            $count++;
            if ($count >= 5) break;
        }
        if ($count == 0) {
            echo "   ❌ No files found in GCS bucket\n";
        } else {
            echo "   ✅ Found " . $count . " files in GCS\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ GCS error: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Generate HTML
    echo "\n5. HTML Generation Test:\n";
    if ($about && $about->image) {
        $imageUrl = config('filesystems.disks.gcs.url') . '/' . $about->image;
        $html = '<img src="' . $imageUrl . '" alt="About Image" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/50\';">';
        echo "   Generated HTML: " . $html . "\n";
    }
    
    // Test 6: Check DataTables response
    echo "\n6. DataTables Response Test:\n";
    try {
        $request = new \Illuminate\Http\Request();
        $request->merge(['_token' => 'test']);
        
        $controller = new \App\Http\Controllers\Mono\AboutController();
        $response = $controller->index($request);
        
        if (method_exists($response, 'getContent')) {
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            if ($data && isset($data['data']) && count($data['data']) > 0) {
                echo "   First record image: " . $data['data'][0]['image'] . "\n";
                echo "   Contains <img tag: " . (strpos($data['data'][0]['image'], '<img') !== false ? 'YES' : 'NO') . "\n";
            } else {
                echo "   No data in response\n";
            }
        }
    } catch (\Exception $e) {
        echo "   ❌ DataTables error: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
