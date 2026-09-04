<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CertificatePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_certificates',
            'create_certificates',
            'edit_certificates',
            'publish_certificates',
            'reorder_certificates',
            'delete_certificates',
        ];

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate(['name' => $name]);
            if (! $superadmin->hasPermissionTo($permission)) {
                $superadmin->givePermissionTo($permission);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
