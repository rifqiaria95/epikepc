<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisasi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\FileStorageService;

class OrganisasiController extends Controller
{
    public function index()
    {
        try {
            // Optimasi: Cache API response untuk organisasi
            $organisasi = Cache::remember('api_organisasi_data', 1800, function() {
                $fileStorage = app(FileStorageService::class);
                return Organisasi::select(['id', 'nama', 'jabatan', 'tahun', 'lokasi', 'deskripsi', 'image', 'created_by', 'created_at', 'updated_at'])
                    ->with(['createdBy:id,name'])
                    ->orderBy('tahun', 'desc')
                    ->get()
                    ->map(function($item) use ($fileStorage) {
                        // Gunakan FileStorageService untuk konsistensi dengan controllers lain
                        if (!empty($item->image)) {
                            $item->image_url = $fileStorage->getFileUrl($item->image);
                        } else {
                            $item->image_url = null;
                        }
                        return $item;
                    });
            });

            return response()->json($organisasi);
        } catch (\Exception $e) {
            Log::error('Error fetching organisasi data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data organisasi',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
