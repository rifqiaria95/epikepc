<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstagramMenuSeeder extends Seeder
{
    public function run(): void
    {
        $newsGroupIds = MenuGroup::query()->where('name', 'News')->pluck('id');

        if ($newsGroupIds->isNotEmpty()) {
            $detailIds = MenuDetail::query()->whereIn('menu_group_id', $newsGroupIds)->pluck('id');

            if ($detailIds->isNotEmpty()) {
                DB::table('menu_detail_permission')->whereIn('menu_detail_id', $detailIds)->delete();
                MenuDetail::query()->whereIn('id', $detailIds)->forceDelete();
            }

            MenuGroup::query()->whereIn('id', $newsGroupIds)->delete();
        }

        MenuDetail::query()
            ->whereIn('route', ['/frontend/news', '/internal/news', '/frontend/news/kategori', '/frontend/news/tag', '/internal/news/kategori', '/internal/news/tag'])
            ->get()
            ->each(function (MenuDetail $detail) {
                $detail->permissions()->detach();
                $detail->forceDelete();
            });

        $group = MenuGroup::query()->firstOrCreate(
            ['name' => 'Content'],
            ['icon' => 'layout-grid', 'order' => 1, 'jenis_menu' => 1]
        );

        $detail = MenuDetail::query()->firstOrCreate(
            ['route' => '/internal/instagram'],
            [
                'menu_group_id' => $group->id,
                'name' => 'Instagram Integration',
                'status' => 1,
                'order' => 6,
            ]
        );

        $permission = Permission::query()->where('name', 'view_instagram')->first();
        if ($permission && method_exists($detail, 'permissions')) {
            $detail->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
