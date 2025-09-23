<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory, SoftDeletes;
    
    protected $table = 'tags';
    protected $fillable = ['name', 'slug'];
    protected $softDeletes = true;
    protected $dates = ['deleted_at'];

    public function news()
    {
        return $this->belongsToMany(News::class, 'news_tag', 'tags_id', 'news_id');
    }
}
