<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_keluar';
    protected $guarded = [];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }
}
