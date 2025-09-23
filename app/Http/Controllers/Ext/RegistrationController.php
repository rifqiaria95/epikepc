<?php

namespace App\Http\Controllers\Ext;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;
use Spatie\Permission\Models\Role;
use App\Services\FileStorageService;

class RegistrationController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Jika request untuk registration page, tampilkan form
        if (!$request->ajax()) {
            return view('external.registration.index');
        }

        // Menampilkan Data user untuk admin - filter user yang punya role 'guest'
        $user = User::withoutTrashed()
            ->with('user_profile')
            ->whereHas('roles', function($query) {
                $query->where('name', 'guest');
            });
        // Cache data user profile untuk referensi
        $user_profile = \Cache::remember('user_profiles_list', 1800, function() {
            return UserProfile::select(['id', 'name', 'email'])->get();
        });

        if ($request->ajax()) {
            return datatables()->of($user)
                ->addColumn('email', function ($data) {
                    return optional($data->user_profile)->email ?? $data->email;
                })
                ->addColumn('nik', function ($data) {
                    return optional($data->user_profile)->nik ?? '-';
                })
                ->addColumn('kk', function ($data) {
                    return optional($data->user_profile)->kk ?? '-';
                })
                ->addColumn('tempat_lahir', function ($data) {
                    return optional($data->user_profile)->tempat_lahir ?? '-';
                })
                ->addColumn('tanggal_lahir', function ($data) {
                    return optional($data->user_profile)->tanggal_lahir ?? '-';
                })
                ->addColumn('alamat', function ($data) {
                    return optional($data->user_profile)->alamat ?? '-';
                })
                ->addColumn('rt', function ($data) {
                    return optional($data->user_profile)->rt ?? '-';
                })
                ->addColumn('active', function ($data) {
                    $status = $data->active ? 'Aktif' : 'Tidak Aktif';
                    $badge = $data->active ? 'success' : 'danger';
                    return '<span class="badge bg-' . $badge . '">' . $status . '</span>';
                })
                ->addColumn('role', function ($data) {
                    // Ambil role dari relasi Spatie Permission
                    $roles = $data->getRoleNames();
                    return $roles->count() > 0 ? ucfirst($roles->first()) : '-';
                })
                ->addColumn('aksi', function ($data) {
                    return route('registration.show', $data->id);
                })
                ->rawColumns(['user_profile', 'aksi', 'email', 'nik', 'kk', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'rt', 'active', 'role'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('external.registration.index', compact(['user', 'user_profile']));
    }

    public function store(RegistrationRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create User tanpa role langsung
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'active' => 0 // Set inactive sampai admin approve
            ]);

            // Pastikan role 'guest' ada, jika tidak buat role baru
            $guestRole = Role::firstOrCreate(['name' => 'guest']);

            // Assign role 'guest' menggunakan Spatie Permission
            $user->assignRole('guest');

            // Create User Profile
            $userProfileData = [
                'user_id'           => $user->id,
                'nik'               => $request->nik,
                'kk'                => $request->kk,
                'tempat_lahir'      => $request->tempat_lahir,
                'tanggal_lahir'     => $request->tanggal_lahir,
                'alamat'            => $request->alamat,
                'rt'                => $request->rt,
                'rw'                => $request->rw,
                'kode_pos'          => $request->kode_pos,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'id_kelurahan'      => $request->id_kelurahan,
                'id_kecamatan'      => $request->id_kecamatan,
                'id_kota'           => $request->id_kota,
                'id_provinsi'       => $request->id_provinsi,
                'no_hp'             => $request->no_hp,
                'pekerjaan'         => $request->pekerjaan,
                'penghasilan'       => $request->penghasilan,
                'status_verifikasi' => 'pending'
            ];

            $userProfile = UserProfile::create($userProfileData);

            // Handle file uploads ke object storage
            if ($request->hasFile('foto_ktp')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('foto_ktp'),
                    'registration/ktp'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload foto KTP: ' . $uploadResult['error']);
                }

                $userProfile->update(['foto_ktp' => $uploadResult['path']]);
            }

            if ($request->hasFile('foto_kk')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('foto_kk'),
                    'registration/kk'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload foto KK: ' . $uploadResult['error']);
                }

                $userProfile->update(['foto_kk' => $uploadResult['path']]);
            }

            DB::commit();

            // Kirim email verifikasi
            event(new Registered($user));

            return response()->json([
                'status' => 200,
                'message' => 'Pendaftaran berhasil! Silakan tunggu verifikasi admin.',
                'data' => $userProfile
            ]);

        } catch (\Exception $e) {
            DB::rollback();

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

    // API Endpoints untuk dropdown wilayah menggunakan Laravolt Indonesia
    public function getProvinsi()
    {
        try {
            $provinsi = Province::select('id_provinsi', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $provinsi
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data provinsi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKota($id_provinsi)
    {
        // Validasi input - pastikan id_provinsi adalah angka dan tidak undefined
        if (empty($id_provinsi) || $id_provinsi === 'undefined' || !is_numeric($id_provinsi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Provinsi tidak valid'
            ], 400);
        }

        // Cek apakah $id_provinsi valid dan ada di database
        $provinsi = Province::where('id_provinsi', $id_provinsi)->first();

        if (!$provinsi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        try {
            $kota = City::where('province_code', $provinsi->code)->get();

            return response()->json([
                'status' => 'success',
                'data' => $kota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKecamatan($id_kota)
    {
        // Validasi input - pastikan id_kota adalah angka dan tidak undefined
        if (empty($id_kota) || $id_kota === 'undefined' || !is_numeric($id_kota)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Kota tidak valid'
            ], 400);
        }

        $kota = City::where('id_kota', $id_kota)->first();

        if (!$kota) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kota tidak ditemukan'
            ], 404);
        }

        try {
            $kecamatan = District::where('city_code', $kota->code)->get();

            return response()->json([
                'status' => 'success',
                'data' => $kecamatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kecamatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKelurahan($id_kecamatan)
    {
        // Validasi input - pastikan id_kecamatan adalah angka dan tidak undefined
        if (empty($id_kecamatan) || $id_kecamatan === 'undefined' || !is_numeric($id_kecamatan)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Kecamatan tidak valid'
            ], 400);
        }

        $kecamatan = District::where('id', $id_kecamatan)->first();

        if (!$kecamatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kecamatan tidak ditemukan'
            ], 404);
        }

        try {
            $kelurahan = Village::where('district_code', $kecamatan->code)->get();

            return response()->json([
                'status' => 'success',
                'data' => $kelurahan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kelurahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('user_profile')->findOrFail($id);

            return view('external.registration.show', compact('user'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with('user_profile')->findOrFail($id);

            return response()->json([
                'user' => $user,
                'user_profile' => $user->user_profile
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function update(RegistrationRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Update user data
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'active' => $request->active ?? $user->active
            ]);

            // Update user profile
            if ($user->user_profile) {
                $user->user_profile->update([
                    'nik'           => $request->nik,
                    'kk'            => $request->kk,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat'        => $request->alamat,
                    'rt'            => $request->rt,
                    'rw'            => $request->rw,
                    'kode_pos'      => $request->kode_pos,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'id_kelurahan'  => $request->id_kelurahan,
                    'id_kecamatan'  => $request->id_kecamatan,
                    'id_kota'       => $request->id_kota,
                    'id_provinsi'   => $request->id_provinsi,
                    'no_hp'         => $request->no_hp,
                    'pekerjaan'     => $request->pekerjaan,
                    'penghasilan'   => $request->penghasilan,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data berhasil diupdate!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Data berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
