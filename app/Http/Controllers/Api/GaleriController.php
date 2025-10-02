<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;

class GaleriController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk galeri
        $galeri = Cache::remember('api_galeri_data', 600, function() {
            $fileStorage = app(FileStorageService::class);
            return Galeri::select(['id', 'title', 'subtitle', 'description', 'image','kategori_galeri_id', 'created_by', 'created_at'])
                ->with(['createdBy:id,name', 'kategoriGaleri:id,name'])
                ->get()
                ->map(function($item) use ($fileStorage) {
                    $item->image_url = !empty($item->image) ? $fileStorage->getFileUrl($item->image) : null;
                    return $item;
                });
        });

        return response()->json($galeri);
    }
}
