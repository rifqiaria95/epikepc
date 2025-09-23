<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriGaleri;

class KategoriGaleriController extends Controller
{
    public function index()
    {
        try {
            // Cache API response untuk kategori galeri
            $kategoriGaleri = \Cache::remember('api_kategori_galeri_data', 1800, function() {
                return KategoriGaleri::select(['id', 'name', 'slug'])
                    ->withCount('galeri')
                    ->orderBy('name', 'asc')
                    ->get()
                    ->filter(function($item) {
                        return $item->galeri_count > 0; // Filter kategori yang memiliki galeri
                    })
                    ->map(function($item) {
                        $item->css_class = $this->getCategoryClass($item->name);
                        return $item;
                    })
                    ->values(); // Reset array keys
            });

            return response()->json($kategoriGaleri);
        } catch (\Exception $e) {
            \Log::error('Error in KategoriGaleriController@index: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getCategoryClass($categoryName)
    {
        if (!$categoryName) return 'design';

        $category = strtolower($categoryName);
        if (strpos($category, 'web') !== false || strpos($category, 'design') !== false) {
            return 'design';
        } else if (strpos($category, 'development') !== false || strpos($category, 'dev') !== false) {
            return 'dev';
        } else if (strpos($category, 'photography') !== false || strpos($category, 'photo') !== false) {
            return 'photography';
        } else {
            return 'design'; // default
        }
    }
}
