<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\PricingRequest;
use App\Models\Pricing;
use App\Models\PricingFeature;
use App\Queries\Internal\InternalSummaryQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PricingController extends Controller
{
    public function index(Request $request, InternalSummaryQuery $summary)
    {
        if ($request->ajax()) {
            $pricing = Pricing::forAdminListing();

            return datatables()->of($pricing)
                ->editColumn('price', function ($data) {
                    return 'Rp '.number_format((float) $data->price, 0, ',', '.');
                })
                ->editColumn('billing_period', function ($data) {
                    return $data->billing_period === 'year' ? 'Year' : 'Bulan';
                })
                ->editColumn('is_popular', function ($data) {
                    return $data->is_popular
                        ? '<span class="badge bg-label-warning">Popular</span>'
                        : '<span class="badge bg-label-secondary">-</span>';
                })
                ->editColumn('is_active', function ($data) {
                    return $data->is_active
                        ? '<span class="badge bg-label-success">Active</span>'
                        : '<span class="badge bg-label-danger">Inactive</span>';
                })
                ->addColumn('features_count', function ($data) {
                    return $data->pricing_features_count;
                })
                ->addColumn('created_by_name', function ($data) {
                    return optional($data->createdBy)->name ?? '-';
                })
                ->addColumn('aksi', function () {
                    return '';
                })
                ->rawColumns(['is_popular', 'is_active', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.pricing.index', [
            'stats' => $summary->cards('pricing'),
        ]);
    }

    public function store(PricingRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $validatedData['created_by'] = Auth::id();
            $validatedData['updated_by'] = Auth::id();

            $pricing = Pricing::create($validatedData);
            $this->syncPricingFeatures($pricing, $request->input('pricing_features', []));

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Pricing saved successfully!',
                'data' => $pricing->loadCount('pricingFeatures'),
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
        try {
            $pricing = Pricing::with([
                'pricingFeatures' => function ($query) {
                    $query->select(['id', 'pricing_id', 'feature', 'sort_order'])
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])->find($id);

            if (! $pricing) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pricing data not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'pricing' => $pricing,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'A server error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, PricingRequest $request)
    {
        try {
            DB::beginTransaction();

            $pricing = Pricing::findOrFail($id);
            $validatedData = $request->validated();
            $validatedData['updated_by'] = Auth::id();

            $pricing->update($validatedData);
            $this->syncPricingFeatures($pricing, $request->input('pricing_features', []));

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Pricing updated successfully',
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

            $pricing = Pricing::find($id);

            if (! $pricing) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Pricing Tidak Ditemukan',
                ]);
            }

            $pricing->deleted_by = Auth::id();
            $pricing->save();
            $pricing->delete();

            PricingFeature::where('pricing_id', $pricing->id)->update([
                'deleted_by' => Auth::id(),
            ]);
            PricingFeature::where('pricing_id', $pricing->id)->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Pricing deleted successfully',
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
     * Replace pricing features in a single transaction-friendly operation.
     */
    protected function syncPricingFeatures(Pricing $pricing, array $features): void
    {
        PricingFeature::where('pricing_id', $pricing->id)->forceDelete();

        $userId = Auth::id();

        foreach ($features as $index => $feature) {
            $featureText = trim($feature['feature'] ?? '');

            if ($featureText === '') {
                continue;
            }

            PricingFeature::create([
                'pricing_id' => $pricing->id,
                'feature' => $featureText,
                'sort_order' => $index,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }
    }
}
