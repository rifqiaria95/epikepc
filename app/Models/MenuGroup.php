<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Permission;

class MenuGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'icon', 'order', 'jenis_menu'];

    protected $table = 'menu_groups';

    public function menuDetails()
    {
        return $this->hasMany(MenuDetail::class, 'menu_group_id', 'id');
    }

    public function subMenuDetails()
    {
        return $this->hasMany(SubMenuDetail::class, 'menu_group_id', 'id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'menu_group_permission', 'menu_group_id', 'permission_id')->withTimestamps();
    }
}
