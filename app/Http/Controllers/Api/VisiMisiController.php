<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisiMisi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VisiMisiController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk about
        $visiMisi = \Cache::remember('api_visi_misi_data', 1800, function() {
            return VisiMisi::select(['id', 'title', 'subtitle', 'description', 'image', 'created_by', 'created_at'])
                ->with(['createdBy:id,name'])
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

                            // Generate GCS URL
                            $item->image_url = config('filesystems.disks.gcs.url') . '/' . $item->image;
                        } else {
                            // Ini adalah file lama yang disimpan di public/images/
                            $imagePath = public_path('images/' . $item->image);
                            if (File::exists($imagePath)) {
                                // Gunakan URL yang dinamis berdasarkan environment dan hindari double slash
                                $baseUrl = config('app.env') === 'production'
                                    ? rtrim(config('app.url'), '/')
                                    : rtrim(url('/'), '/');
                                $item->image_url = $baseUrl . '/images/' . $item->image;
                            } else {
                                $item->image_url = null;
                            }
                        }
                    } else {
                        $item->image_url = null;
                    }
                    return $item;
                });
        });

        return response()->json($visiMisi);
    }
}
