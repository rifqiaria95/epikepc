<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ConsultationPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_consultation',
            'create_consultation',
            'edit_consultation',
            'delete_consultation',
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
