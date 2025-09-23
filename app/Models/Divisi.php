<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Divisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'divisi';
    protected $guarded = [];

    public function departemen()
    {
        return $this->hasMany(Departemen::class, 'id_divisi');
    }
}
