<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitBerat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unit_berat';

    protected $fillable = ['nama', 'simbol'];

    public function item()
    {
        return $this->hasMany(Item::class);
    }
}
