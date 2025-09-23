<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    public function menuGroups(): BelongsToMany
    {
        return $this->belongsToMany(MenuGroup::class, 'menu_group_permission', 'permission_id', 'menu_group_id')->withTimestamps();
    }

    public function menuDetails(): BelongsToMany
    {
        return $this->belongsToMany(MenuDetail::class, 'menu_detail_permission', 'permission_id', 'menu_detail_id')->withTimestamps();
    }
}
