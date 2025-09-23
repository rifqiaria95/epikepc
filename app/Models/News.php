<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    /** @use HasFactory<\Database\Factories\NewsFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'news';
    protected $fillable = ['title', 'slug', 'content', 'summary', 'thumbnail', 'status', 'published_at', 'archived_at', 'author_id', 'created_by', 'updated_by', 'deleted_by', 'path'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Kategori::class, 'category_news', 'news_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag', 'news_id', 'tags_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'news_id');
    }
}
