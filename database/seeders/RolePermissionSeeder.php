<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuGroup;
use App\Models\MenuDetail;
use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('permissions')->delete();
        DB::table('roles')->delete();

        // Ambil semua menu group dan menu detail
        $menuGroups = MenuGroup::all();
        $menuDetails = MenuDetail::all();

        $permissionsToCreate = [];

        // Buat permissions untuk setiap menu group (hanya view)
        foreach ($menuGroups as $menuGroup) {
            $permissionName = 'view_' . strtolower(str_replace([' & ', ' '], '_', $menuGroup->name));
            $permissionsToCreate[] = ['name' => $permissionName];
        }

        // Buat permissions untuk setiap menu detail (view, create, edit, delete, show)
        foreach ($menuDetails as $menuDetail) {
            $baseName = strtolower(str_replace([' & ', ' '], '_', $menuDetail->name));
            $permissionsToCreate[] = ['name' => "view_{$baseName}"];
            $permissionsToCreate[] = ['name' => "create_{$baseName}"];
            $permissionsToCreate[] = ['name' => "edit_{$baseName}"];
            $permissionsToCreate[] = ['name' => "delete_{$baseName}"];
            $permissionsToCreate[] = ['name' => "show_{$baseName}"];
        }

        // Tambahkan izin administratif standar jika belum ada
        $adminPermissions = [
            'view_role', 'create_role', 'edit_role', 'delete_role', 'show_role',
            'view_menu_group', 'create_menu_group', 'edit_menu_group', 'delete_menu_group', 'show_menu_group',
            'view_menu_detail', 'create_menu_detail', 'edit_menu_detail', 'delete_menu_detail', 'show_menu_detail',
            'view_permission', 'create_permission', 'edit_permission', 'delete_permission', 'show_permission',
        ];
        $existingNames = collect($permissionsToCreate)->pluck('name')->toArray();
        foreach ($adminPermissions as $perm) {
            if (!in_array($perm, $existingNames)) {
                $permissionsToCreate[] = ['name' => $perm];
            }
        }

        // Buat role superadmin terlebih dahulu
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin']);
        $guestRole      = Role::firstOrCreate(['name' => 'guest']);

        // Insert semua permission dan langsung kaitkan ke superadmin
        foreach ($permissionsToCreate as $permData) {
            $permission = Permission::firstOrCreate(['name' => $permData['name']]);
            $superadminRole->givePermissionTo($permission);
        }
        $allPermissions = Permission::all();

        // Assign permission yang diawali 'view_' ke admin dan guest
        $viewPermissions = $allPermissions->filter(function($p) {
            return strpos($p->name, 'view_') === 0;
        });
        $adminRole->syncPermissions($viewPermissions);
        $guestRole->syncPermissions($viewPermissions);
    }
}
