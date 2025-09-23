<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokKeluarDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_keluar_detail';
    protected $guarded = [];

    public function stokKeluar()
    {
        return $this->belongsTo(StokKeluar::class, 'id_sk');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}
