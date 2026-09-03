<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceType;
use App\Queries\Internal\InternalSummaryQuery;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService) {}

    public function index(Request $request, InternalSummaryQuery $summary)
    {
        $serviceTypes = ServiceType::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            $query = Service::query()
                ->withoutTrashed()
                ->select(['id', 'title', 'subtitle', 'description', 'image', 'service_type_id', 'created_at'])
                ->with(['serviceType:id,name']);

            return datatables()->of($query)
                ->addColumn('service_type_name', fn ($row) => $row->serviceType?->name ?? '-')
                ->editColumn('image', fn ($row) => $row->getImageUrl())
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.services.index', [
            'service_type' => $serviceTypes,
            'stats' => $summary->cards('services'),
        ]);
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        return $this->persist($request);
    }

    public function edit(string $id): JsonResponse
    {
        try {
            $service = Service::query()
                ->withoutTrashed()
                ->with([
                    'serviceType:id,name',
                    'serviceDetails:id,service_id,title,subtitle,price,description',
                ])
                ->findOrFail($id);

            $serviceData = $service->toArray();
            $serviceData['image_url'] = $service->getImageUrl();
            $serviceData['service_details'] = $service->serviceDetails->toArray();

            return response()->json([
                'success' => true,
                'service' => $serviceData,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(string $id, ServiceRequest $request): JsonResponse
    {
        return $this->persist($request, $id);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $service = Service::query()->withoutTrashed()->findOrFail($id);

            if ($service->image) {
                $this->fileStorageService->deleteFile($service->image);
            }

            ServiceDetail::query()->where('service_id', $service->id)->delete();

            $service->deleted_by = Auth::id();
            $service->save();
            $service->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Service deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->errorResponse($e);
        }
    }

    private function persist(ServiceRequest $request, ?string $id = null): JsonResponse
    {
        $uploadedPath = null;

        try {
            DB::beginTransaction();

            $service = $id
                ? Service::query()->withoutTrashed()->findOrFail($id)
                : new Service;

            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'services/images'
                );

                if (! $uploadResult['success']) {
                    throw new \RuntimeException('Failed to upload image: '.$uploadResult['error']);
                }

                if ($service->exists && $service->image) {
                    $this->fileStorageService->deleteFile($service->image);
                }

                $validated['image'] = $uploadResult['path'];
                $uploadedPath = $uploadResult['path'];
            }

            if ($service->exists) {
                $validated['updated_by'] = Auth::id();
                $service->update($validated);
                $message = 'Service updated successfully.';
            } else {
                $validated['created_by'] = Auth::id();
                $service = Service::create($validated);
                $message = 'Service saved successfully.';
            }

            $this->syncServiceDetails($service, $request->input('service_details', []));

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $message,
                'data' => $service->load(['serviceType:id,name', 'serviceDetails']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($uploadedPath) {
                $this->fileStorageService->deleteFile($uploadedPath);
            }

            return $this->errorResponse($e);
        }
    }

    private function syncServiceDetails(Service $service, array $details): void
    {
        ServiceDetail::query()->where('service_id', $service->id)->delete();

        foreach ($details as $detail) {
            if (empty($detail['title'])) {
                continue;
            }

            ServiceDetail::create([
                'service_id' => $service->id,
                'title' => $detail['title'],
                'subtitle' => $detail['subtitle'] ?? '',
                'price' => $detail['price'] ?? 0,
                'description' => $detail['description'] ?? '',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
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
