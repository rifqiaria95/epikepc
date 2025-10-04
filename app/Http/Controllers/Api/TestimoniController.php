<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimoni;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;

class TestimoniController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk testimoni
        $testimoni = Cache::remember('api_testimoni_data', 1800, function() {
            $fileStorage = app(FileStorageService::class);
            return Testimoni::select(['id', 'nama', 'testimoni', 'instansi', 'gambar', 'created_by', 'created_at', 'updated_at'])
                ->with(['createdBy:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($item) use ($fileStorage) {
                    // Gunakan FileStorageService untuk konsistensi dengan controllers lain
                    if (!empty($item->gambar)) {
                        $item->gambar_url = $fileStorage->getFileUrl($item->gambar);
                    } else {
                        $item->gambar_url = null;
                    }
                    return $item;
                });
        });

        return response()->json($testimoni);
    }
}
