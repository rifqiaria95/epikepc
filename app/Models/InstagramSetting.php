<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class InstagramSetting extends Model
{
    use HasFactory;

    protected $table = 'instagram_settings';

    protected $fillable = [
        'enabled',
        'feed_limit',
        'eyebrow',
        'heading',
        'subtitle',
        'cta_label',
        'view_more_label',
        'profile_url',
        'username',
        'profile_picture_url',
        'account_id',
        'token_status',
        'api_status',
        'last_successful_feed_sync_at',
        'last_successful_story_sync_at',
        'last_failed_sync_at',
        'last_error_class',
        'last_error_message',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'feed_limit' => 'integer',
            'last_successful_feed_sync_at' => 'datetime',
            'last_successful_story_sync_at' => 'datetime',
            'last_failed_sync_at' => 'datetime',
        ];
    }

    public static function current(): self
    {
        $defaults = config('instagram.defaults');

        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'enabled' => true,
                'feed_limit' => (int) config('instagram.feed_limit', 6),
                'eyebrow' => $defaults['eyebrow'],
                'heading' => $defaults['heading'],
                'subtitle' => $defaults['subtitle'],
                'cta_label' => $defaults['cta_label'],
                'view_more_label' => $defaults['view_more_label'],
            ]
        );
    }

    public function lastSuccessfulSyncAt(): ?Carbon
    {
        $feed = $this->last_successful_feed_sync_at;
        $stories = $this->last_successful_story_sync_at;

        if ($feed && $stories) {
            return $feed->greaterThan($stories) ? $feed : $stories;
        }

        return $feed ?: $stories;
    }
}
