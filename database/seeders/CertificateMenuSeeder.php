<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class CertificateMenuSeeder extends Seeder
{
    public function run(): void
    {
        $group = MenuGroup::query()->firstOrCreate(
            ['name' => 'Company'],
            ['icon' => 'building', 'order' => 2, 'jenis_menu' => 6]
        );

        $detail = MenuDetail::query()->firstOrCreate(
            ['route' => '/internal/certificates'],
            [
                'menu_group_id' => $group->id,
                'name' => 'Certificates',
                'status' => 1,
                'order' => 4,
            ]
        );

        $permission = Permission::query()->where('name', 'view_certificates')->first();
        if ($permission && method_exists($detail, 'permissions')) {
            $detail->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
