<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CoveragePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_coverage',
            'create_coverage',
            'edit_coverage',
            'delete_coverage',
            'show_coverage',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $superadmin = Role::where('name', 'superadmin')->first();

            if ($superadmin) {
                $superadmin->givePermissionTo($permission);
            }
        }
    }
}
