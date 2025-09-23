<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProgramRegistRequest;
use App\Models\ProgramRegistration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProgramRegistrationMail;

class ProgramRegistController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data program registrasi dengan filter berdasarkan role
        $currentUser = auth()->user();

        // Jika user memiliki role 'guest', tampilkan hanya data milik user tersebut
        if ($currentUser->hasRole('guest')) {
            $programregist = ProgramRegistration::withoutTrashed()
                ->with('program', 'user')
                ->where('user_id', $currentUser->id);
        } else {
            // Jika selain role 'guest', tampilkan semua data
            $programregist = ProgramRegistration::withoutTrashed()->with('program', 'user');
        }

        if ($request->ajax()) {
            return datatables()->of($programregist)
                ->addColumn('program', function ($data) {
                    return $data->program ? $data->program->name : '-';
                })
                ->addColumn('user_id', function ($data) {
                    return $data->user ? $data->user->name : '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['program', 'aksi', 'user_id'])
                ->addIndexColumn()
                ->toJson();
        }

        // Cache data dropdown yang jarang berubah
        $program = \Cache::remember('programs_list_regist', 1800, function() {
            return Program::select(['id', 'name', 'status'])
                ->whereIn('status', ['active', 'open'])
                ->get();
        });

        $user = \Cache::remember('users_list_regist', 900, function() {
            return User::select(['id', 'name', 'email'])
                ->where('active', true)
                ->get();
        });

        return view('internal/program_regist.index', compact(['program', 'user']));
    }

    public function store(ProgramRegistRequest $request)
    {
        $validatedData = $request->validated();

        // Validasi user sudah pernah daftar program ini
        $sudahDaftar = ProgramRegistration::where('program_id', $validatedData['program_id'])
            ->where('user_id', $validatedData['user_id'])
            ->exists();
        if ($sudahDaftar) {
            return response()->json([
                'status' => 400,
                'message' => 'Anda sudah mendaftar pada program ini'
            ], 400);
        }
        // Validasi kuota
        $program = Program::find($validatedData['program_id']);
        if (!$program) {
            return response()->json([
                'status' => 404,
                'message' => 'Program tidak ditemukan.'
            ], 404);
        }
        $jumlahPendaftar = $program->registrations()->count();
        if ($jumlahPendaftar >= $program->kuota) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf kuota sudah habis'
            ], 400);
        }

        try {
            $programregist = ProgramRegistration::create($validatedData);

            // Kirim email notifikasi pendaftaran
            $user = User::find($validatedData['user_id']);
            $program = Program::find($validatedData['program_id']);

            try {
                Mail::to($user->email)->send(new ProgramRegistrationMail($user, $program));
            } catch (\Exception $e) {
                // Log error email tapi tetap lanjutkan proses
                \Log::error('Gagal mengirim email notifikasi pendaftaran: ' . $e->getMessage());
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $programregist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $programregist = ProgramRegistration::where('id', $id)->first();

        if (!$programregist) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Ambil data program dan user secara terpisah
        $program = null;
        $user = null;

        if ($programregist->program_id) {
            $program = Program::withoutTrashed()->find($programregist->program_id);
        }

        if ($programregist->user_id) {
            $user = User::find($programregist->user_id);
        }

        // Format tanggal untuk input type="date" (YYYY-MM-DD)
        $formattedDate = null;
        if ($programregist->registered_at) {
            try {
                // Coba parse dengan Carbon untuk handling yang lebih baik
                $date = \Carbon\Carbon::parse($programregist->registered_at);
                $formattedDate = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallback ke date() jika Carbon gagal
                $formattedDate = date('Y-m-d', strtotime($programregist->registered_at));
            }
        }

        // Buat response dengan data yang lengkap
        $response = [
            'id'            => $programregist->id,
            'program_id'    => $programregist->program_id,
            'user_id'       => $programregist->user_id,
            'alasan'        => $programregist->alasan,
            'status'        => $programregist->status,
            'notes_admin'   => $programregist->notes_admin,
            'registered_at' => $formattedDate,
            'program'       => $program,
            'user'          => $user
        ];

        return response()->json($response);
    }

    public function update($id, ProgramRegistRequest $request)
    {
        try {
            $programregist = ProgramRegistration::findOrFail($id);

            $validatedData = $request->validated();
            $programregist->update($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data pelanggan berhasil diperbarui',
                'data'    => $programregist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $programregist = ProgramRegistration::where('id', $id)->first();

        if (!$programregist) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Program Regist Tidak Ditemukan'
            ]);
        }

        // Hapus data (Soft Delete)
        $programregist->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Program Regist Berhasil Dihapus'
        ]);
    }
}
