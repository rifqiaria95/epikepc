<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class CareerMenuSeeder extends Seeder
{
    public function run(): void
    {
        $group = MenuGroup::query()->firstOrCreate(
            ['name' => 'Career'],
            ['icon' => 'briefcase', 'order' => 4, 'jenis_menu' => 6]
        );

        $items = [
            ['name' => 'Recruitment Overview', 'route' => '/internal/career', 'order' => 1, 'permission' => 'view_career_dashboard'],
            ['name' => 'Vacancies', 'route' => '/internal/career/vacancies', 'order' => 2, 'permission' => 'view_vacancies'],
            ['name' => 'Applications', 'route' => '/internal/career/applications', 'order' => 3, 'permission' => 'view_applications'],
            ['name' => 'Candidates', 'route' => '/internal/career/candidates', 'order' => 4, 'permission' => 'view_candidates'],
        ];

        foreach ($items as $item) {
            $detail = MenuDetail::query()->firstOrCreate(
                ['route' => $item['route']],
                [
                    'menu_group_id' => $group->id,
                    'name' => $item['name'],
                    'status' => 1,
                    'order' => $item['order'],
                ]
            );

            $permission = Permission::query()->where('name', $item['permission'])->first();
            if ($permission && method_exists($detail, 'permissions')) {
                $detail->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }
}
