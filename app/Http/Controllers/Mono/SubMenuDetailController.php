<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\SubMenuDetail;
use App\Queries\Internal\InternalSummaryQuery;
use Illuminate\Http\Request;

class SubMenuDetailController extends Controller
{
    public function index(Request $request, InternalSummaryQuery $summary)
    {
        // Menampilkan Data pegawai
        $subMenuDetail = SubMenuDetail::with('menuGroup', 'menuDetail', 'menuDetail.menuGroup');
        $menuGroup = MenuGroup::with('menuDetails')->get();
        $menuDetail = MenuDetail::with('menuGroup')->get();
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($subMenuDetail)
                ->addColumn('menu_group_id', function ($data) {
                    return $data->menuGroup->name ?? '-';
                })
                ->addColumn('menu_detail_id', function ($data) {
                    return $data->menuDetail->name ?? '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';

                    return $button;
                })
                ->rawColumns(['menu_group_id', 'menu_detail_id', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/sub_menu_details.index', [
            'subMenuDetail' => $subMenuDetail,
            'menuGroup' => $menuGroup,
            'menuDetail' => $menuDetail,
            'stats' => $summary->cards('sub_menu_details'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $menu = SubMenuDetail::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Menu added successfully!',
            'menu' => $menu,
        ]);
    }

    public function edit($id)
    {
        $menuDetail = SubMenuDetail::find($id);

        if (! $menuDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'menuDetail' => $menuDetail,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $menuDetail = SubMenuDetail::findOrFail($id);
        $menuDetail->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Menu updated successfully!',
        ]);
    }

    public function updateOrder(Request $request)
    {
        $menuDetails = $request->input('menuDetails');

        foreach ($menuDetails as $index => $id) {
            SubMenuDetail::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }

    public function destroy($id)
    {
        $menu_detail = SubMenuDetail::find($id);

        // \ActivityLog::addToLog('Menghapus data menu_detail');

        if ($menu_detail) {
            $menu_detail->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Sub menu detail deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'errors' => 'Error! Sub menu detail data not found',
            ]);
        }
    }
}
