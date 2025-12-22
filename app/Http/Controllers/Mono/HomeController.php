<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\News;
use App\Models\Testimoni;
use App\Services\FileStorageService;

class HomeController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Display homepage with about, published news, and testimonials
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get about data
        $about = About::withoutTrashed()->latest()->first();
        
        // Process about image URL
        if ($about) {
            if ($about->image) {
                try {
                    $about->image_url = $this->fileStorageService->getFileUrl($about->image);
                } catch (\Exception $e) {
                    $about->image_url = asset('frontend/img/bg-img/shape1.jpg');
                }
            } else {
                $about->image_url = asset('frontend/img/bg-img/shape1.jpg');
            }
        }
        
        // Get published news with their relations, limited to 6 items
        $news = News::withoutTrashed()
            ->where('status', 'published')
            ->with(['user', 'categories'])
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();
        
        // Process thumbnail URL for each news item
        $news->each(function ($article) {
            if ($article->thumbnail) {
                try {
                    $article->thumbnail_url = $this->fileStorageService->getFileUrl($article->thumbnail);
                } catch (\Exception $e) {
                    $article->thumbnail_url = asset('frontend/img/bg-img/1.jpg');
                }
            } else {
                $article->thumbnail_url = asset('frontend/img/bg-img/1.jpg');
            }
        });
        
        // Get testimonials data, limited to 10 items
        $testimonials = Testimoni::withoutTrashed()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Image URL sudah di-handle oleh accessor getGambarUrlAttribute() di model
        
        return view('index', compact('about', 'news', 'testimonials'));
    }
}
