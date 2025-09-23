<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departemen';
    protected $guarded = [];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }
}
