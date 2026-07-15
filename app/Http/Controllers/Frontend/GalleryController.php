<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Services\FileStorageService;

class GalleryController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService)
    {
    }

    public function index()
    {
        $items = Galeri::withoutTrashed()
            ->with('kategoriGaleri')
            ->orderByDesc('created_at')
            ->get();

        $items->each(function ($item) {
            if ($item->image) {
                try {
                    $item->image_url = $this->fileStorageService->getFileUrl($item->image);
                } catch (\Exception $e) {
                    $item->image_url = asset('frontend/img/placeholder.jpg');
                }
            } else {
                $item->image_url = asset('frontend/img/placeholder.jpg');
            }
        });

        return view('frontend.gallery.index', compact('items'));
    }
}
