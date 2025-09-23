<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class GaleriController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk galeri
        $galeri = \Cache::remember('api_galeri_data', 1800, function() {
            return Galeri::select(['id', 'title', 'subtitle', 'description', 'image','kategori_galeri_id', 'created_by', 'created_at'])
                ->with(['createdBy:id,name', 'kategoriGaleri:id,name'])
                ->get()
                ->map(function($item) {
                    // Tambahkan URL gambar yang dinamis dengan validasi file
                    if ($item->image) {
                        // Cek apakah ini path storage (mengandung 'uploads/')
                        if (strpos($item->image, 'uploads/') === 0) {
                            // Generate Storage URL - di production mungkin file ada tapi symlink belum dibuat
                            $baseUrl = config('app.env') === 'production'
                                ? rtrim(config('app.url'), '/')
                                : rtrim(url('/'), '/');

                            // Coba Storage URL terlebih dahulu
                            if (Storage::disk('public')->exists($item->image)) {
                                $item->image_url = Storage::disk('public')->url($item->image);
                            } else {
                                // Fallback ke URL manual jika storage symlink belum ada
                                $item->image_url = $baseUrl . '/storage/' . $item->image;
                                \Log::info("Using manual storage URL for: {$item->image}");
                            }
                        } else {
                            // Ini adalah file lama yang disimpan di public/images/
                            $imagePath = public_path('images/' . $item->image);
                            $baseUrl = config('app.env') === 'production'
                                ? rtrim(config('app.url'), '/')
                                : rtrim(url('/'), '/');

                            if (File::exists($imagePath)) {
                                $item->image_url = $baseUrl . '/images/' . $item->image;
                            } else {
                                // Generate URL anyway for production, file might exist
                                $item->image_url = $baseUrl . '/images/' . $item->image;
                                \Log::warning("Image file not found but generating URL anyway: {$item->image}");
                            }
                        }
                    } else {
                        $item->image_url = null;
                    }
                    return $item;
                });
        });

        return response()->json($galeri);
    }
}
