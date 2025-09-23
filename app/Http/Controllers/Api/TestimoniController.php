<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimoni;

class TestimoniController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk testimoni
        $testimoni = \Cache::remember('api_testimoni_data', 1800, function() {
            return Testimoni::select(['id', 'nama', 'testimoni', 'instansi', 'gambar', 'created_by', 'created_at', 'updated_at'])
                ->with(['createdBy:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($item) {
                    // Format gambar URL jika ada
                    if ($item->gambar) {
                        $item->gambar = \Storage::disk('public')->url($item->gambar);
                    }
                    return $item;
                });
        });

        return response()->json($testimoni);
    }
}
