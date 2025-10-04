<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FileStorageService;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    public function index()
    {
        $fileStorage = app(FileStorageService::class);
        $news = Cache::remember('api_news_list', 600, function () use ($fileStorage) {
            return News::where('status', 'published')
                ->with(['tags', 'categories', 'user'])
                ->orderBy('published_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) use ($fileStorage) {
                    // Generate image_url menggunakan FileStorageService seperti AboutController
                    if (!empty($item->thumbnail)) {
                        $item->image_url = $fileStorage->getFileUrl($item->thumbnail);
                    } else {
                        $item->image_url = null;
                    }
                    
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'subtitle' => $item->summary,
                        'description' => $item->content,
                        'image' => $item->image_url ?: '/images/blog/blog-img1.jpg',
                        'image_url' => $item->image_url, // Tambahkan field image_url untuk konsistensi
                        'year' => $item->published_at ? date('Y', strtotime($item->published_at)) : date('Y'),
                        'created_at' => $item->published_at ?: $item->created_at,
                        'updated_at' => $item->updated_at,
                        'tags' => $item->tags->map(function ($tag) {
                            return [
                                'id' => $tag->id,
                                'name' => $tag->name,
                                'slug' => $tag->slug
                            ];
                        }),
                        'categories' => $item->categories->map(function ($category) {
                            return [
                                'id' => $category->id,
                                'name' => $category->name,
                                'slug' => $category->slug
                            ];
                        }),
                        'author' => $item->user ? [
                            'id' => $item->user->id,
                            'name' => $item->user->name
                        ] : null
                    ];
                });
        });

        return response()->json($news);
    }
}
