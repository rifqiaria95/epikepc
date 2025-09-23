<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    /**
     * Relasi ke Permissions
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * Relasi ke Menu Groups
     */
    public function menuGroups(): BelongsToMany
    {
        return $this->belongsToMany(MenuGroup::class, 'menu_group_role', 'role_id', 'menu_group_id')->withTimestamps();
    }

    /**
     * Relasi ke Menu Details
     */
    public function menuDetails(): BelongsToMany
    {
        return $this->belongsToMany(MenuDetail::class, 'menu_detail_role', 'role_id', 'menu_detail_id')->withTimestamps();
    }
}

