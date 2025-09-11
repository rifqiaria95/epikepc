<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                // Generate full URL for thumbnail image
                $imageUrl = null;
                if ($item->thumbnail) {
                    // Check if thumbnail is already a full URL
                    if (str_starts_with($item->thumbnail, 'http://') || str_starts_with($item->thumbnail, 'https://')) {
                        $imageUrl = $item->thumbnail;
                    } else {
                        // Generate full URL using storage disk
                        $imageUrl = \Storage::disk('public')->url($item->thumbnail);
                    }
                }
                
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'subtitle' => $item->summary,
                    'description' => $item->content,
                    'image' => $imageUrl ?: '/images/blog/blog-img1.jpg', // Fallback image
                    'year' => $item->published_at ? date('Y', strtotime($item->published_at)) : date('Y'),
                    'created_at' => $item->published_at ?: $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

        return response()->json($news);
    }
}
