<?php

namespace App\Http\Controllers\Mono;

use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use App\Models\Pegawai;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Perusahaan;
use App\Models\Divisi;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Services\FileStorageService;

class PegawaiController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data pegawai dengan optimasi query
        $pegawai = Pegawai::withoutTrashed()
            ->select(['id', 'nm_pegawai', 'no_telp_pegawai', 'status', 'foto_pegawai', 'user_id', 'id_jabatan', 'id_departemen', 'id_cabang', 'id_divisi', 'id_perusahaan'])
            ->with([
                'user:id,email',
                'jabatan:id,nama_jabatan',
                'departemen:id,nama_departemen',
                'cabang:id,nama_cabang',
                'divisi:id,nama_divisi',
                'perusahaan:id,nama_perusahaan'
            ]);

        // Optimasi untuk dropdown - hanya ambil field yang diperlukan
        $provinsi   = \Cache::remember('provinces_list', 3600, function() {
            return Province::select(['id_provinsi', 'name'])->get();
        });
        $perusahaan = \Cache::remember('companies_list', 1800, function() {
            return Perusahaan::select(['id', 'nama_perusahaan'])->get();
        });
        $divisi     = \Cache::remember('divisions_list', 1800, function() {
            return Divisi::select(['id', 'nama_divisi'])->get();
        });
        $jabatan    = \Cache::remember('positions_list', 1800, function() {
            return Jabatan::select(['id', 'nama_jabatan'])->get();
        });
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($pegawai)
                ->addColumn('email', function ($data) {
                    return optional($data->user)->email ?? '-';
                })
                ->editColumn('foto_pegawai', function ($data) {
                    // Return full storage URL untuk foto_pegawai
                    if ($data->foto_pegawai) {
                        return \Storage::disk('public')->url($data->foto_pegawai);
                    }
                    return null;
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['provinsi', 'aksi', 'email'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/pegawai.index', compact(['pegawai', 'provinsi', 'perusahaan', 'divisi', 'jabatan']));
    }

    public function store(StorePegawaiRequest $request)
    {
        $validatedData = $request->validated();

        try {
            // Insert ke tabel User
            $user = new User();
            $user->assignRole('guest');
            $user->active            = 1;
            $user->name              = $request->nm_pegawai;
            $user->email             = $request->email;
            $user->email_verified_at = now();
            $user->password          = bcrypt('rahasia');
            $user->remember_token    = Str::random(60);
            $user->save();

            // Tambahkan user_id ke $validatedData
            $validatedData['user_id'] = $user->id;

            // Insert ke tabel Pegawai
            $pegawai = Pegawai::create(Arr::except($validatedData, ['email']));

            // Upload Foto Pegawai ke object storage
            if ($request->hasFile('foto_pegawai')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('foto_pegawai'),
                    'pegawai/fotos'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload foto pegawai: ' . $uploadResult['error']);
                }

                $pegawai->foto_pegawai = $uploadResult['path'];
                $pegawai->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Data berhasil disimpan!',
                'data' => $pegawai
            ]);
        } catch (\Exception $e) {
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
        $pegawai = Pegawai::with('user')->where('id', $id)->first();

        return response()->json($pegawai);
    }

    public function update($id, UpdatePegawaiRequest $request)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->validated());

        if ($pegawai) {
            $pegawai->update($request->except('email'));

            $user = User::find($request->user_id);
            if ($user) {
                $user->email = $request->email;
                $user->save();
            }

            if ($request->hasFile('foto_pegawai')) {
                $oldFoto = $pegawai->foto_pegawai;

                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('foto_pegawai'),
                    'pegawai/fotos'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload foto pegawai: ' . $uploadResult['error']);
                }

                $pegawai->foto_pegawai = $uploadResult['path'];
                $pegawai->save();

                // Hapus foto lama jika ada
                if ($oldFoto) {
                    $this->fileStorageService->deleteFile($oldFoto);
                }
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Data pegawai dan email user berhasil diubah'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'errors' => 'Pegawai Tidak Ditemukan'
            ]);
        }
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::where('id', $id)->first();

        if (!$pegawai) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Pegawai Tidak Ditemukan'
            ]);
        }

        // Hapus foto dari object storage jika ada
        if ($pegawai->foto_pegawai) {
            $this->fileStorageService->deleteFile($pegawai->foto_pegawai);
        }

        // Hapus data (Soft Delete)
        $pegawai->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Pegawai Berhasil Dihapus'
        ]);
    }

    public function exportExcelPegawai()
    {
        return Excel::download(new PegawaiExport, 'pegawai.xlsx');
    }

    public function profile($id)
    {
        $pegawai = Pegawai::find($id);
        // Cache data user untuk dropdown (sudah dioptimalkan sebelumnya)
        $user = \Cache::remember('users_list_pegawai', 900, function() {
            return User::select(['id', 'name', 'email'])
                ->where('active', true)
                ->get();
        });

        return view('pegawai.profile', compact(['pegawai', 'user']));
    }

    public function getKotaByProvinsi($id_provinsi)
    {
        // Cari province_code berdasarkan id_provinsi
        $provinsi = Province::where('id_provinsi', $id_provinsi)->first();

        if (!$provinsi) {
            return response()->json(['error' => 'Province tidak ditemukan'], 404);
        }

        // Cari kota berdasarkan province_code
        $kota = City::where('province_code', $provinsi->code)->get();

        return response()->json($kota);
    }

    public function getCabangByPerusahaan($id_perusahaan)
    {
        $cabang = Cabang::where('id_perusahaan', $id_perusahaan)->get();
        return response()->json($cabang);
    }

    public function getDepartemenByDivisi($id_divisi)
    {
        $departemen = Departemen::where('id_divisi', $id_divisi)->get();
        return response()->json($departemen);
    }

    public function formExample()
    {
        // Gunakan cache untuk data yang jarang berubah
        $provinsi = \Cache::remember('provinces_list', 3600, function() {
            return Province::select(['id_provinsi', 'name'])->get();
        });
        $perusahaan = \Cache::remember('companies_list', 1800, function() {
            return Perusahaan::select(['id', 'nama_perusahaan'])->get();
        });
        $divisi = \Cache::remember('divisions_list', 1800, function() {
            return Divisi::select(['id', 'nama_divisi'])->get();
        });
        $jabatan = \Cache::remember('positions_list', 1800, function() {
            return Jabatan::select(['id', 'nama_jabatan'])->get();
        });

        return view('internal.pegawai.form-example', compact(['provinsi', 'perusahaan', 'divisi', 'jabatan']));
    }


}
