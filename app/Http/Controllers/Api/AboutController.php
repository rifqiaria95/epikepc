<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;

class AboutController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk about
        $about = Cache::remember('api_about_data', 600, function() {
            $fileStorage = app(FileStorageService::class);
            return About::select(['id', 'title', 'subtitle', 'description', 'image', 'video', 'address', 'phone', 'email', 'facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'created_by', 'created_at'])
                ->with(['creator:id,name'])
                ->get()
                ->map(function($item) use ($fileStorage) {
                    if (!empty($item->image)) {
                        $item->image_url = $fileStorage->getFileUrl($item->image);
                    } else {
                        $item->image_url = null;
                    }
                    return $item;
                });
        });

        return response()->json($about);
    }
}
