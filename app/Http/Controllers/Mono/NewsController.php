<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\Kategori;
use App\Models\News;
use App\Models\Tag;
use App\Models\User;
use App\Queries\Internal\InternalSummaryQuery;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request, InternalSummaryQuery $summary)
    {
        if ($request->ajax()) {
            // Samakan pendekatan dengan Services: select field yang dibutuhkan,
            // dan kirimkan URL thumbnail (bukan langsung HTML <img>).
            $news = News::select([
                'id',
                'title',
                'slug',
                'content',
                'summary',
                'thumbnail',
                'status',
                'published_at',
                'archived_at',
                'author_id',
            ])
                ->withoutTrashed()
                ->with([
                    'user',
                    'categories',
                    'tags',
                ]);

            return datatables()->of($news)
                ->addColumn('author', function ($data) {
                    return optional($data->user)->name ?? '-';
                })
                ->addColumn('category', function ($data) {
                    return $data->categories->pluck('name')->join(', ') ?: '-';
                })
                ->addColumn('tags', function ($data) {
                    return $data->tags->pluck('name')->join(', ') ?: '-';
                })
                ->editColumn('thumbnail', function ($data) {
                    // Ikuti pendekatan Services: hanya kembalikan URL gambar
                    return $data->getThumbnailUrl();
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';

                    return $button;
                })
                ->rawColumns(['author', 'category', 'tags', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        $kategori = Kategori::select(['id', 'name'])->orderBy('name')->get();
        $tags = Tag::select(['id', 'name'])->orderBy('name')->get();
        $users = Cache::remember('users_author_list', 1800, function () {
            return User::select(['id', 'name'])->where('active', true)->get();
        });

        return view('internal/news.index', [
            'kategori' => $kategori,
            'tags' => $tags,
            'users' => $users,
            'stats' => $summary->cards('news'),
        ]);
    }

    public function store(NewsRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload thumbnail ke object storage jika ada
            if ($request->hasFile('thumbnail')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('thumbnail'),
                    'news/thumbnails'
                );

                if (! $uploadResult['success']) {
                    throw new \Exception('Failed to upload thumbnail: '.$uploadResult['error']);
                }

                $validatedData['thumbnail'] = $uploadResult['path'];
            }

            // Set author_id, created_by berdasarkan user yang sedang login
            $validatedData['author_id'] = Auth::id();
            $validatedData['created_by'] = Auth::id();

            // Auto-fill published_at dan archived_at berdasarkan status
            $this->handleStatusTeamestamps($validatedData);

            // Pisahkan category_id dan tags_id dari validatedData karena tidak ada di tabel news
            $categoryId = Arr::pull($validatedData, 'category_id');
            $tagsId = Arr::pull($validatedData, 'tags_id');

            // Create News (tanpa category_id dan tags_id)
            $news = News::create($validatedData);

            // Attach categories ke tabel pivot jika ada
            if ($categoryId) {
                $news->categories()->attach($categoryId);
            }

            // Attach tags ke tabel pivot jika ada
            if ($tagsId) {
                $news->tags()->attach($tagsId);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'News saved successfully!',
                'data' => $news,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete file yang sudah diupload jika ada error
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->fileStorageService->deleteFile($uploadResult['path']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $news = News::with(['user', 'categories', 'tags'])->where('id', $id)->first();

            if (! $news) {
                return response()->json([
                    'status' => 404,
                    'message' => 'News data not found',
                ], 404);
            }

            // Format data untuk frontend
            $newsData = $news->toArray();

            // Addkan category_id dan tags_id untuk form edit
            $newsData['category_id'] = $news->categories->pluck('id')->toArray();
            $newsData['tags_id'] = $news->tags->pluck('id')->toArray();

            return response()->json([
                'success' => true,
                'news' => $newsData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, NewsRequest $request)
    {
        try {
            DB::beginTransaction();

            $news = News::findOrFail($id);
            $validatedData = $request->validated();
            $oldThumbnail = $news->thumbnail;

            // Upload thumbnail baru ke object storage jika ada
            if ($request->hasFile('thumbnail')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('thumbnail'),
                    'news/thumbnails'
                );

                if (! $uploadResult['success']) {
                    throw new \Exception('Failed to upload thumbnail: '.$uploadResult['error']);
                }

                $validatedData['thumbnail'] = $uploadResult['path'];

                // Delete thumbnail lama jika ada
                if ($oldThumbnail) {
                    $this->fileStorageService->deleteFile($oldThumbnail);
                }
            }

            // Set updated_by berdasarkan user yang sedang login
            $validatedData['updated_by'] = Auth::id();

            // Auto-fill published_at dan archived_at berdasarkan status
            $this->handleStatusTeamestamps($validatedData, $news);

            // Pisahkan category_id dan tags_id dari validatedData
            $categoryId = Arr::pull($validatedData, 'category_id');
            $tagsId = Arr::pull($validatedData, 'tags_id');

            // Update news (tanpa category_id dan tags_id)
            $news->update($validatedData);

            // Sync categories (ganti semua relasi yang ada)
            if ($categoryId !== null) {
                $news->categories()->sync($categoryId);
            }

            // Sync tags (ganti semua relasi yang ada)
            if ($tagsId !== null) {
                $news->tags()->sync($tagsId);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'News updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete file yang sudah diupload jika ada error
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->fileStorageService->deleteFile($uploadResult['path']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $news = News::where('id', $id)->first();

            if (! $news) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data News Tidak Ditemukan',
                ]);
            }

            // Delete thumbnail dari object storage jika ada
            if ($news->thumbnail) {
                $this->fileStorageService->deleteFile($news->thumbnail);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $news->deleted_by = Auth::id();
            $news->save();

            // Delete data (Soft Delete)
            $news->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'News deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle automatic timestamps for published_at and archived_at based on status
     *
     * @return void
     */
    private function handleStatusTeamestamps(array &$validatedData, ?News $existingNews = null)
    {
        $status = $validatedData['status'] ?? null;
        $now = now();

        switch ($status) {
            case 'published':
                // Set published_at jika belum ada or jika status berubah dari non-published ke published
                if (empty($validatedData['published_at'])) {
                    if (! $existingNews || $existingNews->status !== 'published') {
                        $validatedData['published_at'] = $now;
                    } elseif ($existingNews && $existingNews->published_at) {
                        // Pertahankan published_at yang sudah ada jika news sudah published sebelumnya
                        $validatedData['published_at'] = $existingNews->published_at;
                    }
                }
                // Clear archived_at ketika status menjadi published
                $validatedData['archived_at'] = null;
                break;

            case 'archived':
                // Set archived_at jika belum ada or jika status berubah ke archived
                if (empty($validatedData['archived_at'])) {
                    if (! $existingNews || $existingNews->status !== 'archived') {
                        $validatedData['archived_at'] = $now;
                    } elseif ($existingNews && $existingNews->archived_at) {
                        // Pertahankan archived_at yang sudah ada jika news sudah archived sebelumnya
                        $validatedData['archived_at'] = $existingNews->archived_at;
                    }
                }
                break;

            case 'draft':
                // Clear both timestamps ketika status menjadi draft
                $validatedData['published_at'] = null;
                $validatedData['archived_at'] = null;
                break;
        }
    }
}
