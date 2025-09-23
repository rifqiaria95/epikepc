<?php

namespace App\Http\Controllers\Mono;

use App\Models\MenuGroup;
use App\Models\MenuDetail;
use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query role dengan eager loading yang efisien
            $role = Role::select(['id', 'name', 'created_at'])
                ->with(['permissions:id,name']);

            return datatables()->of($role)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi', 'permissions'])
                ->addIndexColumn()
                ->toJson();
        }

        // Cache data dropdown yang jarang berubah
        $menuGroups = \Cache::remember('menu_groups_list', 1800, function() {
            return MenuGroup::select(['id', 'name'])->get();
        });

        $menuGroupIds = $menuGroups->pluck('id')->toArray();
        $menuDetails = \Cache::remember("menu_details_for_groups_" . md5(implode(',', $menuGroupIds)), 1800, function() use ($menuGroupIds) {
            return MenuDetail::select(['id', 'name', 'menu_group_id'])
                ->whereIn('menu_group_id', $menuGroupIds)
                ->get();
        });

        $permissions = \Cache::remember('permissions_with_relations', 900, function() {
            return Permission::select(['id', 'name'])
                ->with([
                    'roles:id,name',
                    'menuGroups:id,name',
                    'menuDetails:id,name'
                ])->get();
        });

        return view('internal/role.index', compact(['permissions', 'menuGroups', 'menuDetails']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            // Buat role baru
            $role = Role::create([
                'name'       => $request->name,
                'guard_name' => 'web',
            ]);

            // Konversi ID permission ke nama permission
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan!',
                'role'    => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan role!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $role = Role::select(['id', 'name'])
            ->with(['permissions:id,name'])
            ->findOrFail($id);

        $permissions = \Cache::remember('permissions_list_edit', 900, function() {
            return Permission::select(['id', 'name'])->get();
        });

        $rolePermissions = $role->permissions->pluck('id');

        return response()->json([
            'role'            => $role,
            'permissions'     => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->input('name')
        ]);

        // Konversi ID permission ke nama permission sebelum sync
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status'  => 404,
                'message' => 'Error! Data role tidak ditemukan'
            ]);
        }

        try {
            $role->delete();
            return response()->json([
                'status'  => 200,
                'message' => 'Sukses! Data role berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function getPermissions()
    {
        $permissions = Permission::with(['menuGroups', 'menuDetails'])
            ->get()
            ->sortBy(function ($permission) {
                return optional($permission->menuGroups->first())->name . optional($permission->menuDetails->first())->name;
            });

        return response()->json([
            'permissions' => $permissions
        ]);
    }


}
