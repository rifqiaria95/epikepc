<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_order';
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class, 'id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
