<?php

return [
    // Match FileStorageService / other CMS uploads (local public disk).
    'disk' => env('CERTIFICATE_UPLOAD_DISK', 'public'),

    'upload_directory' => 'certificate/images',

    'thumbnail_directory' => 'certificate/thumbnails',

    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],

    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],

    'max_file_size_kb' => (int) env('CERTIFICATE_MAX_FILE_SIZE_KB', 5120),

    'max_width' => 8000,

    'max_height' => 8000,

    'max_pixels' => 40_000_000,

    'min_width' => 200,

    'min_height' => 200,

    'thumbnail_max_width' => 480,

    'homepage_max_items' => (int) env('CERTIFICATE_HOMEPAGE_MAX_ITEMS', 100),

    'show_expired_on_frontend' => (bool) env('CERTIFICATE_SHOW_EXPIRED', true),

    'set_sizes' => [
        'xl' => ['min_width' => 1200, 'size' => 8],
        'lg' => ['min_width' => 992, 'size' => 8],
        'md' => ['min_width' => 768, 'size' => 6],
        'sm' => ['min_width' => 0, 'size' => 3],
    ],

    'gesture_threshold_px' => 50,

    'transition_duration_ms' => 450,
];
