<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyJourneyRequest;
use App\Http\Requests\CompanyMilestoneRequest;
use App\Models\CompanyJourney;
use App\Models\CompanyMilestone;
use App\Queries\Internal\InternalSummaryQuery;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService) {}

    public function index(Request $request, InternalSummaryQuery $summary)
    {
        if ($request->ajax() && $request->get('type') === 'milestones') {
            return $this->milestonesDatatable();
        }

        $journey = CompanyJourney::query()->firstOrCreate(
            ['id' => 1],
            CompanyJourney::defaults()
        );

        if ($journey->video_poster) {
            $journey->poster_url = $this->fileStorageService->getFileUrl($journey->video_poster);
        }

        return view('internal.about.index', [
            'journey' => $journey,
            'stats' => $summary->cards('about'),
        ]);
    }

    public function updateJourney(CompanyJourneyRequest $request): JsonResponse
    {
        $uploadedPath = null;

        try {
            DB::beginTransaction();

            $journey = CompanyJourney::query()->firstOrCreate(
                ['id' => 1],
                CompanyJourney::defaults()
            );

            $validated = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', true);

            if ($request->hasFile('video_poster')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('video_poster'),
                    'company-journey/posters'
                );

                if (! $uploadResult['success']) {
                    throw new \RuntimeException('Failed to upload poster: '.$uploadResult['error']);
                }

                if ($journey->video_poster) {
                    $this->fileStorageService->deleteFile($journey->video_poster);
                }

                $validated['video_poster'] = $uploadResult['path'];
                $uploadedPath = $uploadResult['path'];
            }

            $validated['updated_by'] = Auth::id();

            if (! $journey->exists || ! $journey->created_by) {
                $validated['created_by'] = Auth::id();
            }

            $journey->fill($validated);
            $journey->save();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Company Journey settings saved successfully.',
                'data' => $journey->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($uploadedPath) {
                $this->fileStorageService->deleteFile($uploadedPath);
            }

            return $this->errorResponse($e);
        }
    }

    public function storeMilestone(CompanyMilestoneRequest $request): JsonResponse
    {
        return $this->persistMilestone($request);
    }

    public function editMilestone(int $id): JsonResponse
    {
        try {
            $milestone = CompanyMilestone::query()
                ->withoutTrashed()
                ->select(['id', 'year', 'title', 'description', 'sort_order', 'is_active'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'milestone' => $milestone,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function updateMilestone(int $id, CompanyMilestoneRequest $request): JsonResponse
    {
        return $this->persistMilestone($request, $id);
    }

    public function destroyMilestone(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $milestone = CompanyMilestone::query()->withoutTrashed()->findOrFail($id);
            $milestone->deleted_by = Auth::id();
            $milestone->save();
            $milestone->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Milestone deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->errorResponse($e);
        }
    }

    private function milestonesDatatable(): JsonResponse
    {
        $query = CompanyMilestone::query()
            ->withoutTrashed()
            ->select(['id', 'year', 'title', 'description', 'sort_order', 'is_active', 'created_at'])
            ->with(['creator:id,name']);

        return datatables()->of($query)
            ->addColumn('created_by_name', fn ($row) => $row->creator?->name ?? '-')
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-label-success">Active</span>'
                    : '<span class="badge bg-label-secondary">Inactive</span>';
            })
            ->editColumn('description', fn ($row) => str($row->description)->limit(80))
            ->addColumn('aksi', fn () => '')
            ->rawColumns(['is_active', 'aksi'])
            ->addIndexColumn()
            ->toJson();
    }

    private function persistMilestone(CompanyMilestoneRequest $request, ?int $id = null): JsonResponse
    {
        try {
            DB::beginTransaction();

            $milestone = $id
                ? CompanyMilestone::query()->withoutTrashed()->findOrFail($id)
                : new CompanyMilestone;

            $validated = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', true);

            if ($milestone->exists) {
                $validated['updated_by'] = Auth::id();
                $milestone->update($validated);
                $message = 'Milestone updated successfully.';
            } else {
                if (! isset($validated['sort_order'])) {
                    $validated['sort_order'] = (int) CompanyMilestone::query()->max('sort_order') + 1;
                }

                $validated['created_by'] = Auth::id();
                $milestone = CompanyMilestone::create($validated);
                $message = 'Milestone saved successfully.';
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $message,
                'data' => $milestone,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

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
