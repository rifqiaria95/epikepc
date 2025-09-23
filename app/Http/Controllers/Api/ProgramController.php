<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function getOpenPrograms()
    {
        try {
            $programs = Program::with(['jenisProgram', 'user'])
                ->where('status', 'open')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($program) {
                    return [
                        'id' => $program->id,
                        'name' => $program->name,
                        'description' => $program->description,
                        'start_date' => $program->start_date,
                        'end_date' => $program->end_date,
                        'kuota' => $program->kuota,
                        'kategori' => $program->kategori,
                        'sumber_dana' => $program->sumber_dana,
                        'status' => $program->status,
                        'jenis_program' => $program->jenisProgram ? $program->jenisProgram->nama_jenis_program : null,
                        'created_by' => $program->user ? $program->user->name : null,
                        'created_at' => $program->created_at->format('Y-m-d'),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $programs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data program',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
