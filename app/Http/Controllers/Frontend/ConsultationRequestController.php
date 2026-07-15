<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultationSubmissionRequest;
use App\Models\ConsultationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ConsultationRequestController extends Controller
{
    public function store(ConsultationSubmissionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            ConsultationRequest::create($validated);
        });

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih. Permintaan konsultasi Anda sudah kami terima dan akan segera kami hubungi.',
        ]);
    }
}
