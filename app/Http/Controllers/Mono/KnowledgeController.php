<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\KnowledgeRequest;
use App\Models\Knowledge;
use App\Queries\Internal\InternalSummaryQuery;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    public function index(Request $request, InternalSummaryQuery $summary)
    {
        if ($request->ajax()) {
            $knowledge = Knowledge::query()->select(['id', 'question', 'answer', 'created_at']);

            return datatables()->of($knowledge)
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/knowledge.index', [
            'stats' => $summary->cards('knowledge'),
        ]);
    }

    public function store(KnowledgeRequest $request)
    {
        $validatedData = $request->validated();

        $knowledge = Knowledge::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge added successfully!',
            'knowledge' => $knowledge,
        ]);
    }

    public function edit($id)
    {
        $knowledge = Knowledge::findOrFail($id);

        return response()->json([
            'success' => true,
            'knowledge' => $knowledge,
        ]);
    }

    public function update(KnowledgeRequest $request, $id)
    {
        $knowledge = Knowledge::findOrFail($id);
        $knowledge->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Knowledge updated successfully!',
            'knowledge' => $knowledge,
        ]);
    }

    public function destroy($id)
    {
        Knowledge::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Knowledge deleted successfully!',
        ]);
    }
}
