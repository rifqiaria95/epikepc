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

        $defaultDisk = config('filesystems.default');

        // Jika menggunakan GCS
        if ($defaultDisk === 'gcs') {
            $gcsUrl = config('filesystems.disks.gcs.url');
            $bucket = config('filesystems.disks.gcs.bucket');

            if (!empty($gcsUrl)) {
                return rtrim($gcsUrl, '/') . '/' . ltrim($this->thumbnail, '/');
            }

            return 'https://storage.googleapis.com/' . $bucket . '/' . $this->thumbnail;
        }

        // Fallback ke local storage URL
        return url('storage/' . $this->thumbnail);
    }

    /**
     * Accessor agar bisa pakai $news->thumbnail_url langsung.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->getThumbnailUrl();
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
