<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InstagramPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_instagram',
            'manage_instagram',
            'sync_instagram',
            'publish_instagram',
        ];

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate(['name' => $name]);
            if (! $superadmin->hasPermissionTo($permission)) {
                $superadmin->givePermissionTo($permission);
            }
        }

        $retired = [
            'view_news',
            'create_news',
            'edit_news',
            'delete_news',
            'show_news',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'show_categories',
            'view_tags',
            'create_tags',
            'edit_tags',
            'delete_tags',
            'show_tags',
        ];

        $retiredIds = Permission::query()->whereIn('name', $retired)->pluck('id');

        if ($retiredIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $retiredIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $retiredIds)->delete();
            DB::table('menu_detail_permission')->whereIn('permission_id', $retiredIds)->delete();
            Permission::query()->whereIn('id', $retiredIds)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
