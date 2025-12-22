<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Models\ServiceType;
use App\Models\Service;
use App\Models\ServiceDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceRequest;
use App\Http\Requests\ServiceDetailRequest;

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
            $service = Service::select(['id', 'title', 'subtitle', 'description', 'image', 'service_type_id'])
                ->withoutTrashed()
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

        return view('internal/services.index', compact(['service_type']));
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

            // Handle Service Details (multiple)
            if ($request->has('service_details') && is_array($request->input('service_details'))) {
                foreach ($request->input('service_details') as $detail) {
                    if (!empty($detail['title'])) {
                        ServiceDetail::create([
                            'service_id' => $service->id,
                            'title' => $detail['title'],
                            'subtitle' => $detail['subtitle'] ?? '',
                            'price' => $detail['price'] ?? 0,
                            'description' => $detail['description'] ?? '',
                            'created_by' => Auth::user()->id,
                        ]);
                    }
                }
            }

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
            $service = Service::with(['serviceType', 'serviceDetails'])->where('id', $id)->first();

            if (!$service) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data service tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend
            $serviceData = $service->toArray();
            
            // Get service details (multiple)
            $serviceDetails = ServiceDetail::where('service_id', $service->id)->get();
            if ($serviceDetails->count() > 0) {
                $serviceData['service_details'] = $serviceDetails->toArray();
            } else {
                // Backward compatibility: jika masih menggunakan single detail
                $serviceDetail = ServiceDetail::where('service_id', $service->id)->first();
                if ($serviceDetail) {
                    $serviceData['service_detail'] = $serviceDetail->toArray();
                }
            }

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

            // Handle Service Details (multiple) - Hapus yang lama dan buat yang baru
            ServiceDetail::where('service_id', $service->id)->delete();

            if ($request->has('service_details') && is_array($request->input('service_details'))) {
                foreach ($request->input('service_details') as $detail) {
                    if (!empty($detail['title'])) {
                        ServiceDetail::create([
                            'service_id' => $service->id,
                            'title' => $detail['title'],
                            'subtitle' => $detail['subtitle'] ?? '',
                            'price' => $detail['price'] ?? 0,
                            'description' => $detail['description'] ?? '',
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ]);
                    }
                }
            }

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
}
