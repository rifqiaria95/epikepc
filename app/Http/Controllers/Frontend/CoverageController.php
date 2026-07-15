<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CoverageCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CoverageController extends Controller
{
    public function __construct(
        private readonly CoverageCheckService $coverageCheckService
    ) {
    }

    public function suggest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->coverageCheckService->suggest($validated['q']),
        ]);
    }

    public function reverse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $result = $this->coverageCheckService->checkFromCoordinates(
            (float) $validated['lat'],
            (float) $validated['lng']
        );

        return response()->json([
            'success' => true,
            'data' => $result,
            'stats' => $this->coverageCheckService->getStats(),
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location' => ['nullable', 'string', 'min:2', 'max:255', 'required_without:lat,lng'],
            'lat' => ['nullable', 'numeric', 'between:-90,90', 'required_with:lng'],
            'lng' => ['nullable', 'numeric', 'between:-180,180', 'required_with:lat'],
        ]);

        if (isset($validated['lat'], $validated['lng'])) {
            $result = $this->coverageCheckService->checkFromCoordinates(
                (float) $validated['lat'],
                (float) $validated['lng']
            );
        } elseif (! empty($validated['location'])) {
            $result = $this->coverageCheckService->check($validated['location']);
        } else {
            throw ValidationException::withMessages([
                'location' => 'Location is required.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'stats' => $this->coverageCheckService->getStats(),
        ]);
    }
}
