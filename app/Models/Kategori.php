<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    /** @use HasFactory<\Database\Factories\KategoriFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'kategori';
    protected $guarded = [];
    protected $softDeletes = true;
    protected $dates = ['deleted_at'];

    public function item()
    {
        return $this->hasMany(Item::class, 'id');
    }

    public function news()
    {
        return $this->belongsToMany(News::class, 'category_news', 'category_id', 'news_id');
    }
}
