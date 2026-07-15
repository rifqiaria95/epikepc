<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CmsPermissionSeeder extends Seeder
{
    /**
     * CMS permissions used by route middleware (must match web.php exactly).
     */
    private array $resources = [
        'project',
        'services',
        'galeri',
        'profile',
        'news',
        'organisasi',
        'testimoni',
        'pricing',
        'coverage',
        'consultation',
    ];

    public function run(): void
    {
        $permissions = [];

        foreach ($this->resources as $resource) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $permissions[] = "{$action}_{$resource}";
            }
        }

        $permissions[] = 'show_galeri';

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate(['name' => $name]);
            $superadmin->givePermissionTo($permission);

            if (str_starts_with($name, 'view_')) {
                $admin->givePermissionTo($permission);
            }
        }
    }
}
