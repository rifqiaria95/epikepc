<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Permission;

class MenuDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['menu_group_id', 'name', 'status', 'route', 'order'];

    protected $table = 'menu_details';

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function menuGroup()
    {
        return $this->belongsTo(MenuGroup::class, 'menu_group_id');
    }

    public function subMenuDetails()
    {
        return $this->hasMany(SubMenuDetail::class, 'menu_detail_id', 'id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'menu_detail_permission', 'menu_detail_id', 'permission_id')->withTimestamps();
    }
}