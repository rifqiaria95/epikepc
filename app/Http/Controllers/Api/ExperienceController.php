<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Experience;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;

class ExperienceController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk experience
        $experience = Cache::remember('api_experience_data', 600, function() {
            $fileStorage = app(FileStorageService::class);
            return Experience::select(['id', 'title', 'subtitle', 'company', 'year', 'description', 'image', 'created_by', 'created_at'])
                ->with(['createdBy:id,name'])
                ->orderBy('year', 'desc')
                ->get()
                ->map(function($item) use ($fileStorage) {
                    $item->image_url = !empty($item->image) ? $fileStorage->getFileUrl($item->image) : null;
                    return $item;
                });
        });

        return response()->json($experience);
    }
}
