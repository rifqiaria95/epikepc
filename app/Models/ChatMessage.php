<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['session_id', 'role', 'message'];

    public function scopeLatestSession($query)
    {
        return $query->select('session_id')
            ->groupBy('session_id')
            ->latest();
    }
}
