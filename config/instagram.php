<?php

return [
    'enabled' => (bool) env('INSTAGRAM_ENABLED', false),

    'api_base_url' => rtrim((string) env('INSTAGRAM_API_BASE_URL', 'https://graph.instagram.com'), '/'),

    'api_version' => (string) env('INSTAGRAM_API_VERSION', 'v21.0'),

    'user_id' => (string) env('INSTAGRAM_USER_ID', ''),

    'access_token' => (string) env('INSTAGRAM_ACCESS_TOKEN', ''),

    'feed_limit' => (int) env('INSTAGRAM_FEED_LIMIT', 6),

    'story_limit' => (int) env('INSTAGRAM_STORY_LIMIT', 10),

    'highlight_limit' => (int) env('INSTAGRAM_HIGHLIGHT_LIMIT', 10),

    'feed_cache_ttl' => (int) env('INSTAGRAM_FEED_CACHE_TTL', 3600),

    'story_cache_ttl' => (int) env('INSTAGRAM_STORY_CACHE_TTL', 300),

    'http_timeout' => (int) env('INSTAGRAM_HTTP_TIMEOUT', 10),

    'http_retries' => (int) env('INSTAGRAM_HTTP_RETRIES', 2),

    'include_engagement' => (bool) env('INSTAGRAM_INCLUDE_ENGAGEMENT', false),

    'sync_lock_seconds' => (int) env('INSTAGRAM_SYNC_LOCK_SECONDS', 120),

    'upload_directory' => 'instagram/posts',

    'publish_max_kb' => (int) env('INSTAGRAM_PUBLISH_MAX_KB', 102400),

    'publish_video_mimes' => ['video/mp4', 'video/quicktime'],

    'publish_poll_attempts' => (int) env('INSTAGRAM_PUBLISH_POLL_ATTEMPTS', 20),

    'publish_poll_sleep_ms' => (int) env('INSTAGRAM_PUBLISH_POLL_SLEEP_MS', 1500),

    'story_ttl_hours' => 24,

    'image_duration_ms' => 5000,

    'fallback_image' => '/frontend/img/placeholder.jpg',

    'allowed_hosts' => [
        'graph.instagram.com',
        'graph.facebook.com',
    ],

    'defaults' => [
        'eyebrow' => 'Social Media',
        'heading' => 'Follow Our Journey',
        'subtitle' => 'See our latest projects, activities, and stories.',
        'cta_label' => 'Follow us on Instagram',
        'view_more_label' => 'View more on Instagram',
        'profile_url' => 'https://www.instagram.com/',
    ],
];
