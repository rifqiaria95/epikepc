<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\Permission;
use Illuminate\Http\Request;

class MenuDetailController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pegawai
        $menuDetail = MenuDetail::with('menuGroup');
        // Cache data menu group untuk dropdown
        $menuGroup = \Cache::remember('menu_groups_for_details', 1800, function() {
            return MenuGroup::select(['id', 'name'])->get();
        });
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($menuDetail)
                ->addColumn('menu_group_id', function ($data) {
                    return $data->menuGroup->name ?? '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['menu_group_id', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/menu_details.index', compact(['menuDetail', 'menuGroup']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        // Buat menu detail baru
        $menu = MenuDetail::create($request->all());

        // Auto create permissions berdasarkan nama menu
        $this->createMenuPermissions($menu);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan beserta permissions!',
            'menu'    => $menu
        ]);
    }

    /**
     * Membuat permissions otomatis berdasarkan nama menu detail
     */
    private function createMenuPermissions(MenuDetail $menuDetail)
    {
        // Ubah nama menu menjadi lowercase dan replace spasi dengan underscore
        $menuName = strtolower(str_replace(' ', '_', $menuDetail->name));

        // Daftar permission yang akan dibuat
        $permissionTypes = [
            'view_' . $menuName,
            'create_' . $menuName,
            'edit_' . $menuName,
            'show_' . $menuName,
            'delete_' . $menuName,
            'approve_' . $menuName,
            'reject_' . $menuName
        ];

        $createdPermissions = [];

        foreach ($permissionTypes as $permissionName) {
            // Cek apakah permission sudah ada
            $existingPermission = Permission::where('name', $permissionName)->first();

            if (!$existingPermission) {
                // Buat permission baru
                $permission = Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);

                // Hubungkan permission dengan menu detail
                $permission->menuDetails()->attach($menuDetail->id);

                $createdPermissions[] = $permission;
            } else {
                // Jika permission sudah ada, hubungkan dengan menu detail ini juga
                $existingPermission->menuDetails()->syncWithoutDetaching($menuDetail->id);
                $createdPermissions[] = $existingPermission;
            }
        }

        return $createdPermissions;
    }

    public function edit($id)
    {
        $menuDetail = MenuDetail::find($id); // Gunakan find() dulu, bukan findOrFail()

        if (!$menuDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'menuDetail' => $menuDetail
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $menuDetail = MenuDetail::findOrFail($id);
        $oldName = $menuDetail->name;

        // Update menu detail
        $menuDetail->update($request->all());

        // Jika nama berubah, perbarui permissions
        if ($oldName !== $request->name) {
            $this->updateMenuPermissions($menuDetail, $oldName);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui!'
        ]);
    }

    /**
     * Update permissions ketika nama menu detail berubah
     */
    private function updateMenuPermissions(MenuDetail $menuDetail, $oldName)
    {
        $oldMenuName = strtolower(str_replace(' ', '_', $oldName));
        $newMenuName = strtolower(str_replace(' ', '_', $menuDetail->name));

        // Daftar permission yang akan diupdate
        $permissionTypes = ['view_', 'create_', 'edit_', 'show_', 'delete_', 'approve_', 'reject_'];

        foreach ($permissionTypes as $type) {
            $oldPermissionName = $type . $oldMenuName;
            $newPermissionName = $type . $newMenuName;

            // Cari permission lama
            $oldPermission = Permission::where('name', $oldPermissionName)->first();

            if ($oldPermission) {
                // Cek apakah permission baru sudah ada
                $existingNewPermission = Permission::where('name', $newPermissionName)->first();

                if (!$existingNewPermission) {
                    // Update nama permission
                    $oldPermission->update(['name' => $newPermissionName]);
                } else {
                    // Jika permission baru sudah ada, pindahkan relasi ke permission yang sudah ada
                    $existingNewPermission->menuDetails()->syncWithoutDetaching($menuDetail->id);
                    $oldPermission->menuDetails()->detach($menuDetail->id);

                    // Hapus permission lama jika tidak memiliki relasi lain
                    if ($oldPermission->menuDetails()->count() === 0) {
                        $oldPermission->delete();
                    }
                }
            }
        }
    }

    public function updateOrder(Request $request)
    {
        $menuDetails = $request->input('menuDetails');

        foreach ($menuDetails as $index => $id) {
            MenuDetail::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Urutan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $menu_detail = MenuDetail::find($id);

        // \ActivityLog::addToLog('Menghapus data menu_detail');

        if ($menu_detail) {
            // Hapus relasi permissions dengan menu detail ini
            $this->cleanupMenuPermissions($menu_detail);

            // Hapus menu detail
            $menu_detail->delete();

            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data menu_detail dan permissions terkait berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data menu_detail tidak ditemukan'
            ]);
        }
    }

    public function destroyBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:menu_details,id'
        ]);

        try {
            $ids = $request->input('ids');
            $deletedCount = 0;
            $failedCount = 0;

            foreach ($ids as $id) {
                try {
                    $menuDetail = MenuDetail::findOrFail($id);
                    
                    // Hapus relasi permissions dengan menu detail ini
                    $this->cleanupMenuPermissions($menuDetail);
                    
                    // Hapus menu detail
                    $menuDetail->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }

            $message = "Berhasil menghapus {$deletedCount} menu detail";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} menu detail gagal dihapus";
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

    /**
     * Membersihkan permissions yang terkait dengan menu detail
     */
    private function cleanupMenuPermissions(MenuDetail $menuDetail)
    {
        $menuName = strtolower(str_replace(' ', '_', $menuDetail->name));

        // Daftar permission yang akan dibersihkan
        $permissionTypes = [
            'view_' . $menuName,
            'create_' . $menuName,
            'edit_' . $menuName,
            'show_' . $menuName,
            'delete_' . $menuName,
            'approve_' . $menuName,
            'reject_' . $menuName
        ];

        foreach ($permissionTypes as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                // Hapus relasi dengan menu detail ini
                $permission->menuDetails()->detach($menuDetail->id);

                // Jika permission tidak memiliki relasi dengan menu detail lain, hapus permission
                if ($permission->menuDetails()->count() === 0 && $permission->menuGroups()->count() === 0) {
                    // Hapus juga relasi dengan roles jika ada
                    $permission->roles()->detach();
                    $permission->delete();
                }
            }
        }
    }

}

