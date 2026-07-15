<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoverageLocationRequest;
use App\Models\CoverageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoverageLocationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $locations = CoverageLocation::forAdminListing();

            return datatables()->of($locations)
                ->editColumn('kabupaten', fn ($row) => $row->kabupaten ?: '-')
                ->editColumn('kelurahan', fn ($row) => $row->kelurahan ?: '-')
                ->editColumn('type', fn ($row) => $row->type_label)
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-label-success">Active</span>'
                        : '<span class="badge bg-label-danger">Inactive</span>';
                })
                ->addColumn('created_by_name', fn ($row) => optional($row->createdBy)->name ?? '-')
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['is_active', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.coverage.index');
    }

    public function options()
    {
        $kabupaten = CoverageLocation::query()
            ->covered()
            ->whereNotNull('kabupaten')
            ->distinct()
            ->orderBy('kabupaten')
            ->pluck('kabupaten');

        $kelurahan = CoverageLocation::query()
            ->covered()
            ->whereNotNull('kelurahan')
            ->select(['kabupaten', 'kelurahan'])
            ->distinct()
            ->orderBy('kabupaten')
            ->orderBy('kelurahan')
            ->get()
            ->groupBy('kabupaten')
            ->map(fn ($items) => $items->pluck('kelurahan')->values());

        return response()->json([
            'success' => true,
            'kabupaten' => $kabupaten,
            'kelurahan' => $kelurahan,
        ]);
    }

    public function store(CoverageLocationRequest $request)
    {
        try {
            DB::beginTransaction();

            $location = CoverageLocation::create([
                ...$this->payload($request),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Coverage location saved successfully!',
                'data' => $location,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $location = CoverageLocation::query()->find($id);

        if (! $location) {
            return response()->json([
                'status' => 404,
                'message' => 'Coverage location data not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }

    public function update($id, CoverageLocationRequest $request)
    {
        try {
            DB::beginTransaction();

            $location = CoverageLocation::findOrFail($id);
            $location->update([
                ...$this->payload($request),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Coverage location updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $location = CoverageLocation::query()->find($id);

            if (! $location) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Coverage location data not found',
                ], 404);
            }

            $location->deleted_by = Auth::id();
            $location->save();
            $location->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Coverage location deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(CoverageLocationRequest $request): array
    {
        $validated = $request->validated();
        $isReference = $validated['type'] === CoverageLocation::TYPE_REFERENCE;

        return [
            'kabupaten' => $isReference ? null : $validated['kabupaten'],
            'kelurahan' => $isReference ? null : $validated['kelurahan'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];
    }
}
