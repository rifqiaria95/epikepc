<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceTypeRequest;
use App\Models\ServiceType;
use App\Queries\Internal\InternalSummaryQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceTypeController extends Controller
{
    public function index(Request $request, InternalSummaryQuery $summary)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $service_type = ServiceType::select(['id', 'name', 'type', 'created_at']);

            return datatables()->of($service_type)
                ->addColumn('aksi', function ($data) {
                    $button = '';

                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/service_type.index', [
            'stats' => $summary->cards('service_types'),
        ]);
    }

    public function store(ServiceTypeRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by'] = Auth::user()->id;

        $service_type = ServiceType::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Service type added successfully!',
            'service_type' => $service_type,
        ]);
    }

    public function edit($id)
    {
        $service_type = ServiceType::findOrFail($id);

        return response()->json([
            'success' => true,
            'service_type' => $service_type,
        ]);
    }

    public function update(ServiceTypeRequest $request, $id)
    {
        $validatedData = $request->validated();
        $validatedData['updated_by'] = Auth::user()->id;

        $service_type = ServiceType::findOrFail($id);
        $service_type->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Service type updated successfully!',
        ]);
    }

    public function destroy($id)
    {
        $service_type = ServiceType::find($id);

        // \ActivityLog::addToLog('Menghapus data service type');

        if ($service_type) {
            $service_type->deleted_by = Auth::user()->id;
            $service_type->save();
            $service_type->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Service type deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'errors' => 'Error! Service type data not found',
            ]);
        }
    }
}
