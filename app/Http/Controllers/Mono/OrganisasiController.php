<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Services\FileStorageService;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use App\Http\Requests\OrganisasiRequest;
use Illuminate\Support\Facades\DB;

class OrganisasiController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data organisasi
        $organisasi = Organisasi::withoutTrashed()->with(['createdBy', 'updatedBy', 'deleter']);

        if ($request->ajax()) {
            return datatables()->of($organisasi)
                ->addColumn('created_by', function ($data) {
                    return optional($data->createdBy)->name ?? '-';
                })
                ->addColumn('updated_by', function ($data) {
                    return optional($data->updatedBy)->name ?? '-';
                })
                ->addColumn('deleted_by', function ($data) {
                    return optional($data->deleter)->name ?? '-';
                })
                ->editColumn('image', function ($data) {
                    // Return HTML img tag untuk image
                    if ($data->image) {
                        $imageUrl = $this->fileStorageService->getFileUrl($data->image);
                        return '<img src="' . $imageUrl . '" alt="Organisasi Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['created_by', 'updated_by', 'deleted_by', 'image', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/organisasi.index', compact(['organisasi']));
    }

    public function store(OrganisasiRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload image ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'organisasi/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Failed to upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];
            }

            // Set created_by berdasarkan user yang sedang login
            $validatedData['created_by'] = auth()->id();

            // Create About
            $organisasi = Organisasi::create($validatedData);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Team member saved successfully!',
                'data' => $organisasi
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $organisasi = Organisasi::with(['createdBy', 'updatedBy', 'deleter'])->where('id', $id)->first();

            if (!$organisasi) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Organization data not found'
                ], 404);
            }

            // Format data untuk frontend
            $organisasiData = $organisasi->toArray();

            return response()->json([
                'success' => true,
                'organisasi' => $organisasiData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, OrganisasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $organisasi = Organisasi::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $organisasi->image;

            // Upload image baru ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'organisasi/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Failed to upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];

                // Delete image lama jika ada
                if ($oldImage) {
                    $this->fileStorageService->deleteFile($oldImage);
                }
            }

            // Set updated_by berdasarkan user yang sedang login
            $validatedData['updated_by'] = auth()->id();

            // Update about
            $organisasi->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Team member updated successfully'
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $organisasi = Organisasi::where('id', $id)->first();

            if (!$organisasi) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Organisasi Tidak Ditemukan'
                ]);
            }

            // Delete image dari object storage jika ada
            if ($organisasi->image) {
                $this->fileStorageService->deleteFile($organisasi->image);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $organisasi->deleted_by = auth()->id();
            $organisasi->save();

            // Delete data (Soft Delete)
            $organisasi->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Organization deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
