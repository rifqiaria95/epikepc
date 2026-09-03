<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultationRequestStoreRequest;
use App\Models\ConsultationRequest;
use App\Queries\Internal\InternalSummaryQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationRequestController extends Controller
{
    public function index(Request $request, InternalSummaryQuery $summary)
    {
        if ($request->ajax()) {
            $consultations = ConsultationRequest::forAdminListing();

            return datatables()->of($consultations)
                ->editColumn('source', fn ($row) => $row->source_label)
                ->editColumn('status', function ($row) {
                    $class = match ($row->status) {
                        ConsultationRequest::STATUS_CONTACTED => 'bg-label-info',
                        ConsultationRequest::STATUS_CLOSED => 'bg-label-success',
                        default => 'bg-label-warning',
                    };

                    return '<span class="badge '.$class.'">'.e($row->status_label).'</span>';
                })
                ->addColumn('created_by_name', fn ($row) => optional($row->createdBy)->name ?? '-')
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['status', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.consultation.index', [
            'stats' => $summary->cards('consultation'),
        ]);
    }

    public function store(ConsultationRequestStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $consultation = ConsultationRequest::create([
                ...$request->validated(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Consultation saved successfully.',
                'data' => $consultation,
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

    public function edit(int $id)
    {
        $consultation = ConsultationRequest::query()->find($id);

        if (! $consultation) {
            return response()->json([
                'status' => 404,
                'message' => 'Consultation data not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'consultation' => $consultation,
        ]);
    }

    public function update(int $id, ConsultationRequestStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $consultation = ConsultationRequest::query()->findOrFail($id);
            $consultation->update([
                ...$request->validated(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Consultation updated successfully.',
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

    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();

            $consultation = ConsultationRequest::query()->find($id);

            if (! $consultation) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Consultation data not found.',
                ], 404);
            }

            $consultation->deleted_by = Auth::id();
            $consultation->save();
            $consultation->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Consultation deleted successfully.',
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
}
