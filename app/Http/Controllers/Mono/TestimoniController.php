<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Models\Testimoni;
use App\Http\Requests\TestimoniRequest;
use Illuminate\Support\Facades\DB;

class TestimoniController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data about
        $testimoni = Testimoni::withoutTrashed()->with(['createdBy', 'updatedBy', 'deletedBy']);

        if ($request->ajax()) {
            return datatables()->of($testimoni)
                ->addColumn('created_by', function ($data) {
                    return optional($data->createdBy)->name ?? '-';
                })
                ->addColumn('updated_by', function ($data) {
                    return optional($data->updatedBy)->name ?? '-';
                })
                ->addColumn('deleted_by', function ($data) {
                    return optional($data->deletedBy)->name ?? '-';
                })
                ->editColumn('gambar', function ($data) {
                    // Return full storage URL untuk image
                    if ($data->gambar) {
                        return \Storage::disk('public')->url($data->gambar);
                    }
                    return null;
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['created_by', 'updated_by', 'deleted_by', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/testimoni.index', compact(['testimoni']));
    }

    public function store(TestimoniRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload image ke object storage jika ada
            if ($request->hasFile('gambar')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('gambar'),
                    'testimoni/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload gambar: ' . $uploadResult['error']);
                }

                $validatedData['gambar'] = $uploadResult['path'];
            }

            // Set created_by dan updated_by berdasarkan user yang sedang login
            $validatedData['created_by'] = auth()->id();
            $validatedData['updated_by'] = auth()->id();

            // Create Testimoni
            $testimoni = Testimoni::create($validatedData);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data testimoni berhasil disimpan!',
                'data' => $testimoni
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
            $testimoni = Testimoni::with(['createdBy', 'updatedBy', 'deletedBy'])->where('id', $id)->first();

            if (!$testimoni) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data testimoni tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend
            $testimoniData = $testimoni->toArray();

            return response()->json([
                'success' => true,
                'testimoni' => $testimoniData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, TestimoniRequest $request)
    {
        try {
            DB::beginTransaction();

            $testimoni = Testimoni::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $testimoni->gambar;

            // Upload image baru ke object storage jika ada
            if ($request->hasFile('gambar')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('gambar'),
                    'testimoni/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload gambar: ' . $uploadResult['error']);
                }

                $validatedData['gambar'] = $uploadResult['path'];

                // Hapus image lama jika ada
                if ($oldImage) {
                    $this->fileStorageService->deleteFile($oldImage);
                }
            }

            // Set updated_by berdasarkan user yang sedang login
            $validatedData['updated_by'] = auth()->id();

            // Update testimoni
            $testimoni->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Data testimoni berhasil diubah'
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

            $testimoni = Testimoni::where('id', $id)->first();

            if (!$testimoni) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Testimoni Tidak Ditemukan'
                ]);
            }

            // Hapus image dari object storage jika ada
            if ($testimoni->gambar) {
                $this->fileStorageService->deleteFile($testimoni->gambar);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $testimoni->deleted_by = auth()->id();
            $testimoni->save();

            // Hapus data (Soft Delete)
            $testimoni->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data Testimoni Berhasil Dihapus'
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
