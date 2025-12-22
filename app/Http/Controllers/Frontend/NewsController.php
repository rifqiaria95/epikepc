<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Kategori;
use App\Models\Tag;
use App\Services\FileStorageService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Display news detail page
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Get news by slug with relations
        $news = News::withoutTrashed()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['user', 'categories', 'tags'])
            ->firstOrFail();

        // Process thumbnail URL
        if ($news->thumbnail) {
            try {
                $news->thumbnail_url = $this->fileStorageService->getFileUrl($news->thumbnail);
            } catch (\Exception $e) {
                $news->thumbnail_url = asset('frontend/img/bg-img/117.jpg');
            }
        } else {
            $news->thumbnail_url = asset('frontend/img/bg-img/117.jpg');
        }

        // Get recent posts (exclude current)
        $recentPosts = News::withoutTrashed()
            ->where('status', 'published')
            ->where('id', '!=', $news->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Process thumbnail URL for recent posts
        $recentPosts->each(function ($article) {
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

        // Get all categories with news count
        $categories = Kategori::withCount(['news' => function ($query) {
            $query->where('status', 'published');
        }])->get();

        // Get all tags that have published news
        $allTags = Tag::whereHas('news', function ($query) {
            $query->where('status', 'published')
                  ->whereNull('news.deleted_at');
        })
        ->withCount(['news' => function ($query) {
            $query->where('status', 'published');
        }])
        ->orderBy('name')
        ->get();

        return view('frontend.news.detail', compact('news', 'recentPosts', 'categories', 'allTags'));
    }
}
