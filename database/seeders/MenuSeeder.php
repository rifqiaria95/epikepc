<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use App\Models\MenuGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_details')->delete();
        DB::table('menu_groups')->delete();

        $groups = [
            ['name' => 'Content', 'icon' => 'layout-grid', 'order' => 1, 'jenis_menu' => 1],
            ['name' => 'Company', 'icon' => 'building', 'order' => 2, 'jenis_menu' => 2],
            ['name' => 'News', 'icon' => 'news', 'order' => 3, 'jenis_menu' => 3],
            ['name' => 'Marketing', 'icon' => 'speakerphone', 'order' => 4, 'jenis_menu' => 4],
            ['name' => 'Admin', 'icon' => 'lock-heart', 'order' => 5, 'jenis_menu' => 7],
        ];

        $menuGroups = [];
        foreach ($groups as $data) {
            $menuGroups[$data['name']] = MenuGroup::create($data);
        }

        $details = [
            // Content
            ['name' => 'Projects', 'route' => '/frontend/project', 'status' => 1, 'order' => 1, 'group' => 'Content'],
            ['name' => 'Services', 'route' => '/services/service_list', 'status' => 1, 'order' => 2, 'group' => 'Content'],
            ['name' => 'Service Types', 'route' => '/services/service_type', 'status' => 1, 'order' => 3, 'group' => 'Content'],
            ['name' => 'Gallery', 'route' => '/frontend/galeri/list-galeri', 'status' => 1, 'order' => 4, 'group' => 'Content'],
            ['name' => 'Gallery Categories', 'route' => '/frontend/galeri/kategori-galeri', 'status' => 1, 'order' => 5, 'group' => 'Content'],

            // Company
            ['name' => 'Company Journey', 'route' => '/frontend/profile/about', 'status' => 1, 'order' => 1, 'group' => 'Company'],
            ['name' => 'Team', 'route' => '/frontend/organisasi', 'status' => 1, 'order' => 2, 'group' => 'Company'],
            ['name' => 'Testimonials', 'route' => '/frontend/testimoni', 'status' => 1, 'order' => 3, 'group' => 'Company'],

            // News
            ['name' => 'News', 'route' => '/frontend/news', 'status' => 1, 'order' => 1, 'group' => 'News'],
            ['name' => 'Categories', 'route' => '/frontend/news/kategori', 'status' => 1, 'order' => 2, 'group' => 'News'],
            ['name' => 'Tags', 'route' => '/frontend/news/tag', 'status' => 1, 'order' => 3, 'group' => 'News'],

            // Marketing
            ['name' => 'Pricing', 'route' => '/frontend/pricing', 'status' => 1, 'order' => 1, 'group' => 'Marketing'],
            ['name' => 'Coverage Areas', 'route' => '/frontend/coverage', 'status' => 1, 'order' => 2, 'group' => 'Marketing'],
            ['name' => 'Consultations', 'route' => '/frontend/consultation', 'status' => 1, 'order' => 3, 'group' => 'Marketing'],

            // Admin
            ['name' => 'User Management', 'route' => '/admin/users', 'status' => 1, 'order' => 1, 'group' => 'Admin'],
            ['name' => 'Role Management', 'route' => '/admin/role', 'status' => 1, 'order' => 2, 'group' => 'Admin'],
            ['name' => 'Permission Management', 'route' => '/admin/permission', 'status' => 1, 'order' => 3, 'group' => 'Admin'],
            ['name' => 'Menu Group', 'route' => '/admin/menu-group', 'status' => 1, 'order' => 4, 'group' => 'Admin'],
            ['name' => 'Menu Detail', 'route' => '/admin/menu', 'status' => 1, 'order' => 5, 'group' => 'Admin'],
            ['name' => 'Knowledge Base', 'route' => '/admin/knowledge', 'status' => 1, 'order' => 6, 'group' => 'Admin'],
            ['name' => 'Trash', 'route' => '/deleted/data', 'status' => 1, 'order' => 7, 'group' => 'Admin'],
        ];

        foreach ($details as $detail) {
            $groupName = $detail['group'];
            unset($detail['group']);

            if (! isset($menuGroups[$groupName])) {
                continue;
            }

            MenuDetail::create(array_merge($detail, [
                'menu_group_id' => $menuGroups[$groupName]->id,
            ]));
        }
    }
}
