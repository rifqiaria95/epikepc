<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class News extends Model
{
    /** @use HasFactory<\Database\Factories\NewsFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'news';
    protected $fillable = ['title', 'slug', 'content', 'summary', 'thumbnail', 'status', 'published_at', 'archived_at', 'author_id', 'created_by', 'updated_by', 'deleted_by'];
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Generate public URL for thumbnail (mirip dengan Service::getImageUrl()).
     *
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        if (!$this->thumbnail) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail);
    }

    /**
     * Accessor agar bisa pakai $news->thumbnail_url langsung.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->getThumbnailUrl() ?: asset('frontend/img/blog/blog-thumb-1.png');
    }

    /**
     * Query published news for homepage display with eager-loaded relations.
     */
    public function scopeForHomepage($query, int $limit = 3)
    {
        return $query
            ->select(['id', 'title', 'slug', 'summary', 'thumbnail', 'status', 'published_at', 'author_id', 'created_at'])
            ->withoutTrashed()
            ->where('status', 'published')
            ->with(['user:id,name'])
            ->orderByDesc('published_at')
            ->limit($limit);
    }

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
