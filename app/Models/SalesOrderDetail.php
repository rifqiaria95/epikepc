<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesOrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_order_detail';
    protected $guarded = [];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}
