<?php

namespace App\Http\Controllers\Ext;

use App\Http\Controllers\Controller;
use App\Models\ProgramRegistration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProgramRegistrationMail;
use App\Http\Requests\ProgramRegistRequest;

class ProgramRegistController extends Controller
{
    public function index(Request $request)
    {
        // Jika request untuk registration page, tampilkan form
        if (!$request->ajax()) {
            $program = Program::where('status', 'open')->get();
            return view('external.program-regist.index', compact('program'));
        }
    }

    public function store(ProgramRegistRequest $request)
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Anda harus login terlebih dahulu untuk mendaftar program'
            ], 401);
        }

        $validatedData = $request->validated();
        
        // Set data yang diperlukan untuk external registration
        $validatedData['user_id'] = auth()->id();
        $validatedData['registered_at'] = now();
        $validatedData['status'] = 'pending';

        // Debug: Log data yang akan disimpan
        \Log::info('Program Registration Data:', $validatedData);

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
            \Log::error('Error creating program registration: ' . $e->getMessage());
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $programregist = ProgramRegistration::with('program', 'user')->where('id', $id)->first();

        if (!$programregist) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($programregist);
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
