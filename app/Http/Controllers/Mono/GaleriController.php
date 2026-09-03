<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\GaleriRequest;
use App\Models\Galeri;
use App\Models\KategoriGaleri;
use App\Queries\Internal\InternalSummaryQuery;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GaleriController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService) {}

    public function index(Request $request, InternalSummaryQuery $summary)
    {
        $kategoriGaleri = KategoriGaleri::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            $query = Galeri::query()
                ->withoutTrashed()
                ->select([
                    'id', 'title', 'subtitle', 'description', 'image',
                    'kategori_galeri_id', 'created_by', 'updated_by', 'created_at',
                ])
                ->with([
                    'createdBy:id,name',
                    'kategoriGaleri:id,name',
                ]);

            return datatables()->of($query)
                ->addColumn('created_by', fn ($row) => $row->createdBy?->name ?? '-')
                ->addColumn('kategori_galeri', fn ($row) => $row->kategoriGaleri?->name ?? '-')
                ->editColumn('image', function ($row) {
                    if (! $row->image) {
                        return null;
                    }

                    return $this->fileStorageService->getFileUrl($row->image);
                })
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.galeri.index', [
            'kategoriGaleri' => $kategoriGaleri,
            'stats' => $summary->cards('galeri'),
        ]);
    }

    public function store(GaleriRequest $request): JsonResponse
    {
        return $this->persist($request);
    }

    public function edit(string $id): JsonResponse
    {
        try {
            $galeri = Galeri::query()
                ->withoutTrashed()
                ->select([
                    'id', 'title', 'subtitle', 'description', 'image',
                    'kategori_galeri_id', 'created_by', 'updated_by',
                ])
                ->with(['kategoriGaleri:id,name'])
                ->findOrFail($id);

            $data = $galeri->toArray();
            $data['image_url'] = $galeri->image
                ? $this->fileStorageService->getFileUrl($galeri->image)
                : null;

            return response()->json([
                'success' => true,
                'galeri' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(string $id, GaleriRequest $request): JsonResponse
    {
        return $this->persist($request, $id);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $galeri = Galeri::query()->withoutTrashed()->findOrFail($id);

            if ($galeri->image) {
                $this->fileStorageService->deleteFile($galeri->image);
            }

            $galeri->deleted_by = auth()->id();
            $galeri->save();
            $galeri->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Gallery item deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->errorResponse($e);
        }
    }

    private function persist(GaleriRequest $request, ?string $id = null): JsonResponse
    {
        $uploadedPath = null;

        try {
            DB::beginTransaction();

            $galeri = $id
                ? Galeri::query()->withoutTrashed()->findOrFail($id)
                : new Galeri;

            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'galeri/images'
                );

                if (! $uploadResult['success']) {
                    throw new \RuntimeException('Failed to upload image: '.$uploadResult['error']);
                }

                if ($galeri->exists && $galeri->image) {
                    $this->fileStorageService->deleteFile($galeri->image);
                }

                $validated['image'] = $uploadResult['path'];
                $uploadedPath = $uploadResult['path'];
            }

            if ($galeri->exists) {
                $validated['updated_by'] = auth()->id();
                $galeri->update($validated);
                $message = 'Gallery item updated successfully.';
            } else {
                $validated['created_by'] = auth()->id();
                $galeri = Galeri::create($validated);
                $message = 'Gallery item saved successfully.';
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $message,
                'data' => $galeri,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($uploadedPath) {
                $this->fileStorageService->deleteFile($uploadedPath);
            }

            return $this->errorResponse($e);
        }
    }

    private function errorResponse(\Throwable $e): JsonResponse
    {
        return response()->json([
            'status' => 500,
            'message' => 'A server error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
