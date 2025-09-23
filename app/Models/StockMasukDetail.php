<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMasukDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_masuk_detail';
    protected $guarded = [];

    public function stockMasuk()
    {
        return $this->belongsTo(StockMasuk::class, 'id_sm');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}
