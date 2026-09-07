<?php

namespace App\Models;

use Database\Factories\KategoriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    /** @use HasFactory<KategoriFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'kategori';

    protected $guarded = [];

    protected $softDeletes = true;

    protected $dates = ['deleted_at'];

    public function item()
    {
        return $this->hasMany(Item::class, 'id');
    }
}
