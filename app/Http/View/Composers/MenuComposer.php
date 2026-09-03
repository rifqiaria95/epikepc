<?php

namespace App\Http\View\Composers;

use App\Models\MenuGroup;
use App\Support\Menu\MenuActiveMatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuComposer
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $menuGroups = collect();

        if (Auth::check()) {
            $user = Auth::user();
            $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

            // Jika user bukan super admin, filter berdasarkan permission
            if (! $user->hasRole('superadmin')) {
                // Ambil semua menu groups dan filter menu details berdasarkan permission
                $menuGroups = MenuGroup::with(['menuDetails' => function ($query) {
                    $query->orderBy('order', 'asc')->with(['subMenuDetails', 'permissions']);
                }])
                    ->orderBy('order', 'asc')
                    ->get()
                    ->map(function ($menuGroup) use ($userPermissions) {
                        // Filter menu details berdasarkan permission user
                        $filteredMenuDetails = $menuGroup->menuDetails->filter(function ($menuDetail) use ($userPermissions) {
                            // Cek apakah user memiliki permission untuk menu detail ini
                            return $menuDetail->permissions->whereIn('id', $userPermissions)->isNotEmpty();
                        });

                        // Set menu details yang sudah difilter
                        $menuGroup->setRelation('menuDetails', $filteredMenuDetails);

                        return $menuGroup;
                    })
                    ->filter(function ($menuGroup) {
                        // Hanya tampilkan menu group yang memiliki menu detail dengan permission
                        return $menuGroup->menuDetails->isNotEmpty();
                    });
            } else {
                // Jika super admin, tampilkan semua menu
                $menuGroups = MenuGroup::with(['menuDetails' => function ($query) {
                    $query->orderBy('order', 'asc')->with('subMenuDetails');
                }])->orderBy('order', 'asc')->get();
            }
        }

        $view->with([
            'menuGroups' => $menuGroups,
            'menuActive' => app(MenuActiveMatcher::class),
        ]);
    }
}
