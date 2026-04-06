<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Thumbnail Debug (public storage) ===\n";

try {
    echo "1. Public disk configuration:\n";
    echo '   URL base: '.config('filesystems.disks.public.url')."\n";
    echo '   Root: '.config('filesystems.disks.public.root')."\n";

    echo "\n2. About Model Data:\n";
    $about = \App\Models\About::first();
    if ($about && $about->image) {
        echo '   Image path: '.$about->image."\n";
        echo '   Generated URL: '.\Illuminate\Support\Facades\Storage::disk('public')->url($about->image)."\n";
    } else {
        echo "   No about data found or no image\n";
    }

    echo "\n2b. Specific Image Path Test:\n";
    $specificPath = 'uploads/2025/10/about/images/QuhFvLIQx5cIg97CVYCpsj8TBAgS1PJ24S0Vxoww.JPG';
    echo '   Testing path: '.$specificPath."\n";
    echo '   Generated URL: '.\Illuminate\Support\Facades\Storage::disk('public')->url($specificPath)."\n";

    echo "\n3. URL Accessibility Test:\n";
    $testPath = $about && $about->image ? $about->image : $specificPath;
    if ($testPath) {
        $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($testPath);
        echo '   Testing URL: '.$imageUrl."\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo '   HTTP Status: '.$httpCode."\n";
        if ($httpCode == 200) {
            echo "   ✅ Image URL is accessible\n";
        } else {
            echo "   ❌ Image URL is not accessible\n";
        }
    }

    echo "\n4. Public disk files (sample under uploads/):\n";
    try {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $files = $disk->files('uploads');
        $count = count($files);
        if ($count === 0) {
            $all = $disk->allFiles('uploads');
            $count = min(5, count($all));
            foreach (array_slice($all, 0, 5) as $f) {
                echo '   File: '.$f."\n";
            }
            if (count($all) === 0) {
                echo "   ⚠ No files found under uploads/ (or directory empty)\n";
            }
        } else {
            foreach (array_slice($files, 0, 5) as $f) {
                echo '   File: '.$f."\n";
            }
        }
    } catch (\Exception $e) {
        echo '   ❌ Storage error: '.$e->getMessage()."\n";
    }

    echo "\n5. HTML Generation Test:\n";
    if ($about && $about->image) {
        $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($about->image);
        $html = '<img src="'.$imageUrl.'" alt="About Image" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/50\';">';
        echo '   Generated HTML: '.$html."\n";
    }

    echo "\n6. DataTables Response Test:\n";
    try {
        $request = new \Illuminate\Http\Request;
        $request->merge(['_token' => 'test']);

        $controller = new \App\Http\Controllers\Mono\AboutController;
        $response = $controller->index($request);

        if (method_exists($response, 'getContent')) {
            $content = $response->getContent();
            $data = json_decode($content, true);

            if ($data && isset($data['data']) && count($data['data']) > 0) {
                echo '   First record image: '.$data['data'][0]['image']."\n";
                echo '   Contains <img tag: '.(strpos($data['data'][0]['image'], '<img') !== false ? 'YES' : 'NO')."\n";
            } else {
                echo "   No data in response\n";
            }
        }
    } catch (\Exception $e) {
        echo '   ❌ DataTables error: '.$e->getMessage()."\n";
    }

} catch (\Exception $e) {
    echo "\n❌ Error: ".$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}
