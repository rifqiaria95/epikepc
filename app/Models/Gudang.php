<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gudang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gudang';
    protected $guarded = [];

    public function stok()
    {
        return $this->hasMany(Stok::class, 'id');
    }
}
