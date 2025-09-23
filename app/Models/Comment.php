<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'comment';
    protected $fillable = ['news_id', 'user_id', 'comment'];
    protected $softDeletes = true;
    protected $dates = ['deleted_at'];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
