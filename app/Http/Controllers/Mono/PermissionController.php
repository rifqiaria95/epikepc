<?php

namespace App\Http\Controllers\Mono;

use App\Models\MenuGroup;
use App\Models\MenuDetail;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query permissions dengan eager loading efisien
            $permissions = Permission::select(['id', 'name', 'created_at'])
                ->with([
                    'roles:id,name',
                    'menuGroups:id,name',
                    'menuDetails:id,name'
                ]);

            return datatables()->of($permissions)
                ->addColumn('assigned_to', function ($permission) {
                    return $permission->roles ? $permission->roles->pluck('name')->toArray() : [];
                })
                ->addColumn('menu_groups', function ($permission) {
                    return $permission->menuGroups ? $permission->menuGroups->pluck('name')->toArray() : [];
                })
                ->addColumn('menu_details', function ($permission) {
                    return $permission->menuDetails ? $permission->menuDetails->pluck('name')->toArray() : [];
                })
                ->addColumn('aksi', function ($permission) {
                    return ''; // Kolom aksi akan di-render di JavaScript
                })
                ->rawColumns(['aksi'])
                ->toJson();
        }

        // Cache data dropdown yang jarang berubah
        $menuGroups = \Cache::remember('menu_groups_permissions', 1800, function() {
            return MenuGroup::select(['id', 'name'])->get();
        });
        $menuDetails = \Cache::remember('menu_details_permissions', 1800, function() {
            return MenuDetail::select(['id', 'name'])->get();
        });

        return view('internal/permission.index', compact('menuGroups', 'menuDetails'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255|unique:permissions,name',
            'roles'           => 'array',
            'menu_groups'  => 'required|string|max:255',
            'menu_details' => 'required|string|max:255',
        ]);

        // Buat permission baru
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->save();

        // Hubungkan dengan roles
        if ($request->has('roles')) {
            $permission->roles()->sync($request->roles);
        }

        // Hubungkan dengan menuGroups
        if ($request->has('menu_groups')) {
            $permission->menuGroups()->sync($request->menu_groups);
        }

        // Hubungkan dengan menuDetails
        if ($request->has('menu_details')) {
            $permission->menuDetails()->sync($request->menu_details);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Permission berhasil ditambahkan!',
            'permission' => $permission
        ]);
    }


    public function edit($id)
    {
        $permission = Permission::with(['roles', 'menuGroups', 'menuDetails'])->findOrFail($id);

        return response()->json([
            'success'    => true,
            'permission' => [
                'id'           => $permission->id,
                'name'         => $permission->name,
                'menu_groups'  => $permission->menuGroups->pluck('id'), // Ambil hanya ID
                'menu_details' => $permission->menuDetails->pluck('id') // Ambil hanya ID
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:255|unique:permissions,name,' . $id,
            'roles'        => 'array',
            'menu_groups'  => 'required|string|max:255',
            'menu_details' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();

        // Update roles
        if ($request->has('roles')) {
            $permission->roles()->sync($request->roles);
        }

        // Update menuGroups
        if ($request->has('menu_groups')) {
            $permission->menuGroups()->sync($request->menu_groups);
        }

        // Update menuDetails
        if ($request->has('menu_details')) {
            $permission->menuDetails()->sync($request->menu_details);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        try {
            // Cari permission, jika tidak ditemukan langsung return 404
            $permission = Permission::findOrFail($id);

            $permission->roles()->detach();
            $permission->menuDetails()->detach();
            $permission->menuGroups()->detach();

            // Hapus permission
            $permission->delete();

            return response()->json([
                'status'  => 200,
                'message' => 'Sukses! Data permission berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function destroyBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:permissions,id'
        ]);

        try {
            $ids = $request->input('ids');
            $deletedCount = 0;
            $failedCount = 0;

            foreach ($ids as $id) {
                try {
                    $permission = Permission::findOrFail($id);
                    
                    // Detach relationships
                    $permission->roles()->detach();
                    $permission->menuDetails()->detach();
                    $permission->menuGroups()->detach();
                    
                    // Delete permission
                    $permission->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }

            $message = "Berhasil menghapus {$deletedCount} permission";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} permission gagal dihapus";
            }

            return response()->json([
                'status' => 200,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'failed_count' => $failedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getMenuGroups()
    {
        $menuGroups = MenuGroup::select('id', 'name')->get();
        return response()->json($menuGroups);
    }

    public function getMenuDetails()
    {
        $menuDetails = MenuDetail::select('id', 'name')->get();
        return response()->json($menuDetails);
    }

    public function getMenuDetailsByGroup(Request $request)
    {
        $menuGroupId = $request->input('menu_group_id');

        if (!$menuGroupId) {
            return response()->json([]);
        }

        $menuDetails = MenuDetail::where('menu_group_id', $menuGroupId)
            ->select('id', 'name')
            ->get();

        return response()->json($menuDetails);
    }


}
