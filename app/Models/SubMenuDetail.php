<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Permission;

class SubMenuDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['menu_group_id', 'menu_detail_id', 'name', 'status', 'route', 'order'];

    protected $table = 'sub_menu_details';

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function menuGroup()
    {
        return $this->belongsTo(MenuGroup::class, 'menu_group_id');
    }

    public function menuDetail()
    {
        return $this->belongsTo(MenuDetail::class, 'menu_detail_id');
    }
}
