<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceRequest;

class ServicesController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Cache data dropdown yang jarang berubah
        $service_type = ServiceType::select(['id', 'name'])->get();

        if ($request->ajax()) {
            // Temporary fix: Simplified query without specific select fields
            $service = Service::withoutTrashed()
                ->with([
                    'serviceType'
                ]);

            return datatables()->of($service)
                ->editColumn('image', function ($data) {
                    return $data->getImageUrl();
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['image', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        // Get all services for frontend display
        $services = Service::withoutTrashed()
            ->with(['serviceType'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.services.index', compact(['service_type', 'services']));
    }

    public function store(ServiceRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload thumbnail ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'services/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];
            }

            // Set author_id, created_by berdasarkan user yang sedang login
            $validatedData['created_by'] = Auth::user()->id;

            // Create Service
            $service = Service::create($validatedData);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data service berhasil disimpan!',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->fileStorageService->deleteFile($uploadResult['path']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $service = Service::with(['serviceType'])->where('id', $id)->first();

            if (!$service) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data service tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend
            $serviceData = $service->toArray();

            return response()->json([
                'success' => true,
                'service' => $serviceData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, ServiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $service = Service::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $service->image;

            // Upload thumbnail baru ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'services/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];

                // Hapus image lama jika ada
                if ($oldImage) {
                    $this->fileStorageService->deleteFile($oldImage);
                }
            }

            // Set updated_by berdasarkan user yang sedang login
            $validatedData['updated_by'] = Auth::user()->id;

            // Update service
            $service->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Data service berhasil diubah'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->fileStorageService->deleteFile($uploadResult['path']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $service = Service::where('id', $id)->first();

            if (!$service) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Service Tidak Ditemukan'
                ]);
            }

            // Hapus image dari object storage jika ada
            if ($service->image) {
                $this->fileStorageService->deleteFile($service->image);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $service->deleted_by = Auth::user()->id;
            $service->save();

            // Hapus data (Soft Delete)
            $service->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data Service Berhasil Dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display services by service type (slug-based for SEO)
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function showByServiceType(string $slug)
    {
        // Get service type by slug
        $serviceType = ServiceType::where('slug', $slug)->firstOrFail();

        // Get all services under this service type
        $services = Service::withoutTrashed()
            ->where('service_type_id', $serviceType->id)
            ->with(['serviceType', 'serviceDetails'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all service types for sidebar/navigation
        $allServiceTypes = ServiceType::orderBy('type')
            ->orderBy('name')
            ->get();

        return view('frontend.services.by-type', compact('serviceType', 'services', 'allServiceTypes'));
    }

    /**
     * Display service detail page
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function detailService($id)
    {
        // Get service by id with relations
        $service = Service::withoutTrashed()
            ->with(['serviceType', 'serviceDetails'])
            ->where('id', '=', $id)
            ->firstOrFail();

        // Get recent services (exclude current)
        $recentServices = Service::withoutTrashed()
            ->where('id', '!=', $service->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        return view('frontend.services.detail', compact('service', 'recentServices'));
    }

    /**
     * Display news detail page
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function kaiflowServices()
    {
        // Get news by slug with relations
        $service = Service::withoutTrashed()
            ->where('id', '=', 2)
            ->firstOrFail();

        // Get recent posts (exclude current)
        $recentServices = Service::withoutTrashed()
            ->where('id', '!=', $service->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        return view('frontend.services.kaiflow.index', compact('service', 'recentServices'));
    }
}
