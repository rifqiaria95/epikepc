<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public    $incrementing = false;
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    protected $table        = 'item';
    protected $guarded      = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function unit_berat()
    {
        return $this->belongsTo(UnitBerat::class, 'id_unit_berat');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function stok()
    {
        return $this->hasMany(Stok::class, 'id');
    }

    public function purchaseOrder()
    {
        return $this->hasMany(PurchaseOrder::class, 'id');
    }

    public function salesOrder()
    {
        return $this->hasMany(SalesOrder::class, 'id');
    }

    public function stockMasuk()
    {
        return $this->hasMany(StockMasuk::class, 'id');
    }

    public function stockKeluar()
    {
        return $this->hasMany(StockKeluar::class, 'id');
    }
}
