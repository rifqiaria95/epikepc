<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisiMisi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;

class VisiMisiController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk about
        $visiMisi = Cache::remember('api_visi_misi_data', 600, function() {
            $fileStorage = app(FileStorageService::class);
            return VisiMisi::select(['id', 'title', 'subtitle', 'description', 'image', 'created_by', 'created_at'])
                ->with(['createdBy:id,name'])
                ->get()
                ->map(function($item) use ($fileStorage) {
                    $item->image_url = !empty($item->image) ? $fileStorage->getFileUrl($item->image) : null;
                    return $item;
                });
        });

        return response()->json($visiMisi);
    }
}
