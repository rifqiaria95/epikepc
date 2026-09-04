<?php

namespace App\Http\Controllers\Mono;

use App\Enums\CertificateStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Certificate\CertificateReorderRequest;
use App\Http\Requests\Certificate\CertificateStoreRequest;
use App\Models\Certificate;
use App\Queries\Certificate\CertificateCmsQuery;
use App\Queries\Internal\InternalSummaryQuery;
use App\Services\Certificate\CertificateImageService;
use App\Services\Certificate\CertificateManagementService;
use App\Services\FileStorageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;

class CertificateController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected CertificateCmsQuery $query,
        protected CertificateManagementService $manager,
        protected CertificateImageService $images,
        protected FileStorageService $storage,
        protected InternalSummaryQuery $summary,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Certificate::class);

        if ($request->ajax()) {
            $rows = $this->query->baseQuery($request);

            return datatables()->of($rows)
                ->editColumn('status', function (Certificate $row) {
                    $class = match ($row->status) {
                        CertificateStatus::Published => 'bg-label-success',
                        CertificateStatus::Archived => 'bg-label-dark',
                        default => 'bg-label-warning',
                    };

                    return '<span class="badge '.$class.'">'.e($row->status->label()).'</span>';
                })
                ->editColumn('is_featured', fn (Certificate $row) => $row->is_featured
                    ? '<span class="badge bg-label-info">Featured</span>'
                    : '<span class="text-muted">—</span>')
                ->editColumn('image_path', function (Certificate $row) {
                    $path = $row->thumbnail_path ?: $row->image_path;
                    if (! $path) {
                        return null;
                    }

                    return $this->storage->getFileUrl($path);
                })
                ->editColumn('issued_at', fn (Certificate $row) => optional($row->issued_at)?->format('d M Y') ?? '—')
                ->editColumn('expires_at', fn (Certificate $row) => optional($row->expires_at)?->format('d M Y') ?? '—')
                ->editColumn('published_at', fn (Certificate $row) => optional($row->published_at)?->format('d M Y H:i') ?? '—')
                ->addColumn('created_by_name', fn (Certificate $row) => $row->createdBy?->name ?? '—')
                ->addColumn('updated_by_name', fn (Certificate $row) => $row->updatedBy?->name ?? '—')
                ->addColumn('homepage_visible', fn (Certificate $row) => $row->status === CertificateStatus::Published
                    && ($row->published_at === null || $row->published_at->lte(now()))
                    ? '<span class="badge bg-label-success">Yes</span>'
                    : '<span class="badge bg-label-secondary">No</span>')
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['status', 'is_featured', 'homepage_visible', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.certificate.index', [
            'stats' => $this->summary->cards('certificates'),
            'statusOptions' => CertificateStatus::options(),
        ]);
    }

    public function store(CertificateStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Certificate::class);

        return $this->persist($request);
    }

    public function edit(string $id): JsonResponse
    {
        $certificate = Certificate::query()->withoutTrashed()->findOrFail($id);
        $this->authorize('view', $certificate);

        $data = $certificate->toArray();
        $data['image_url'] = $certificate->image_path
            ? $this->storage->getFileUrl($certificate->image_path)
            : null;

        return response()->json([
            'success' => true,
            'certificate' => $data,
        ]);
    }

    public function update(string $id, CertificateStoreRequest $request): JsonResponse
    {
        $certificate = Certificate::query()->withoutTrashed()->findOrFail($id);
        $this->authorize('update', $certificate);

        return $this->persist($request, $certificate);
    }

    public function destroy(string $id): JsonResponse
    {
        $certificate = Certificate::query()->withoutTrashed()->findOrFail($id);
        $this->authorize('delete', $certificate);

        try {
            $this->manager->softDelete($certificate, request()->user());

            return response()->json([
                'status' => 200,
                'message' => 'Sertifikat berhasil dihapus.',
            ]);
        } catch (\Throwable) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan server.',
            ], 500);
        }
    }

    public function publish(string $id): JsonResponse
    {
        $certificate = Certificate::query()->withoutTrashed()->findOrFail($id);
        $this->authorize('publish', $certificate);

        try {
            $this->manager->publish($certificate, request()->user());

            return response()->json([
                'status' => 200,
                'message' => 'Sertifikat berhasil dipublikasikan.',
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['status' => 422, 'message' => $e->getMessage()], 422);
        }
    }

    public function unpublish(string $id): JsonResponse
    {
        $certificate = Certificate::query()->withoutTrashed()->findOrFail($id);
        $this->authorize('publish', $certificate);

        $this->manager->unpublish($certificate, request()->user());

        return response()->json([
            'status' => 200,
            'message' => 'Sertifikat dikembalikan ke draft.',
        ]);
    }

    public function archive(string $id): JsonResponse
    {
        $certificate = Certificate::query()->withoutTrashed()->findOrFail($id);
        $this->authorize('publish', $certificate);

        $this->manager->archive($certificate, request()->user());

        return response()->json([
            'status' => 200,
            'message' => 'Sertifikat diarsipkan.',
        ]);
    }

    public function reorder(CertificateReorderRequest $request): JsonResponse
    {
        $this->authorize('reorder', Certificate::class);

        try {
            $this->manager->reorder($request->validated('ordered_ids'), $request->user());

            return response()->json([
                'status' => 200,
                'message' => 'Urutan sertifikat diperbarui.',
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['status' => 422, 'message' => $e->getMessage()], 422);
        }
    }

    private function persist(CertificateStoreRequest $request, ?Certificate $certificate = null): JsonResponse
    {
        $uploadedImage = null;
        $uploadedThumb = null;
        $oldImage = $certificate?->image_path;
        $oldThumb = $certificate?->thumbnail_path;

        try {
            $validated = $request->validated();
            $status = isset($validated['status'])
                ? CertificateStatus::from($validated['status'])
                : ($certificate?->status ?? CertificateStatus::Draft);

            if ($status === CertificateStatus::Published) {
                $hasImage = $request->hasFile('image') || filled($certificate?->image_path);
                $hasAlt = filled($validated['image_alt'] ?? $certificate?->image_alt);

                if (! $hasImage || ! $hasAlt) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Gambar dan teks alternatif wajib diisi sebelum sertifikat dipublikasikan.',
                        'errors' => [
                            'image' => $hasImage ? [] : ['Gambar sertifikat wajib diunggah sebelum dipublikasikan.'],
                            'image_alt' => $hasAlt ? [] : ['Teks alternatif wajib diisi sebelum sertifikat dipublikasikan.'],
                        ],
                    ], 422);
                }
            }

            if ($request->hasFile('image')) {
                $upload = $this->images->upload($request->file('image'));
                if (! $upload['success']) {
                    return response()->json([
                        'status' => 422,
                        'message' => $upload['error'],
                        'errors' => ['image' => [$upload['error']]],
                    ], 422);
                }

                $validated['image_path'] = $upload['path'];
                $validated['thumbnail_path'] = $upload['thumbnail_path'] ?? null;
                $uploadedImage = $upload['path'];
                $uploadedThumb = $upload['thumbnail_path'] ?? null;
            }

            unset($validated['image']);

            if ($status === CertificateStatus::Published && empty($validated['published_at']) && ! $certificate?->published_at) {
                $validated['published_at'] = now();
            }

            $validated['status'] = $status;
            $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

            return DB::transaction(function () use ($certificate, $validated, $request, $oldImage, $oldThumb, $uploadedImage, $uploadedThumb) {
                if ($certificate) {
                    $certificate = $this->manager->update($certificate, $validated, $request->user());

                    if ($uploadedImage && $oldImage) {
                        $this->images->deletePaths($oldImage, $oldThumb);
                    }

                    $message = 'Sertifikat berhasil diperbarui.';
                } else {
                    if (empty($validated['image_path'])) {
                        throw new InvalidArgumentException('Gambar sertifikat wajib diunggah.');
                    }

                    $certificate = $this->manager->create($validated, $request->user());
                    $message = 'Sertifikat berhasil disimpan.';
                }

                return response()->json([
                    'status' => 200,
                    'message' => $message,
                    'data' => $certificate,
                ]);
            });
        } catch (InvalidArgumentException $e) {
            if ($uploadedImage) {
                $this->images->deletePaths($uploadedImage, $uploadedThumb);
            }

            return response()->json([
                'status' => 422,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable) {
            if ($uploadedImage) {
                $this->images->deletePaths($uploadedImage, $uploadedThumb);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan server.',
            ], 500);
        }
    }
}
