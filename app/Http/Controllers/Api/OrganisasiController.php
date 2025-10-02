<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisasi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OrganisasiController extends Controller
{
    public function index()
    {
        try {
            // Optimasi: Cache API response untuk organisasi
            $organisasi = \Cache::remember('api_organisasi_data', 1800, function() {
                return Organisasi::select(['id', 'nama', 'jabatan', 'tahun', 'lokasi', 'deskripsi', 'image', 'created_by', 'created_at', 'updated_at'])
                    ->with(['createdBy:id,name'])
                    ->orderBy('tahun', 'desc')
                    ->get()
                    ->map(function($item) {
                        // Tambahkan URL gambar yang dinamis dengan validasi file
                        if ($item->image) {
                            // Jika image sudah berupa URL lengkap (dari accessor model)
                            if (filter_var($item->image, FILTER_VALIDATE_URL)) {
                                $item->image_url = $item->image;
                            } else {
                                // Cek apakah ini path storage (mengandung 'uploads/' atau 'organisasi/')
                                if (strpos($item->image, 'uploads/') === 0 || strpos($item->image, 'organisasi/') === 0) {
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
                            }
                        } else {
                            $item->image_url = null;
                        }
                        return $item;
                    });
            });

            return response()->json($organisasi);
        } catch (\Exception $e) {
            \Log::error('Error fetching organisasi data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data organisasi',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
