<?php

use App\Enums\InstagramErrorClass;
use App\Enums\InstagramMediaType;
use App\Support\Instagram\InstagramCaptionSanitizer;
use App\Support\Instagram\InstagramLogger;
use App\Support\Instagram\InstagramResponseNormalizer;

it('normalizes feed image, carousel children, video, and reels', function () {
    $normalizer = new InstagramResponseNormalizer;

    $image = $normalizer->media([
        'id' => '111',
        'caption' => 'Plant walkdown',
        'media_type' => 'IMAGE',
        'media_url' => 'https://scontent.cdninstagram.com/image.jpg',
        'permalink' => 'https://www.instagram.com/p/abc/',
        'timestamp' => '2026-09-01T08:00:00+0000',
    ]);

    $carousel = $normalizer->media([
        'id' => '222',
        'media_type' => 'CAROUSEL_ALBUM',
        'media_url' => 'https://scontent.cdninstagram.com/cover.jpg',
        'permalink' => 'https://www.instagram.com/p/def/',
        'timestamp' => '2026-09-01T09:00:00+0000',
        'children' => [
            'data' => [
                ['id' => '222-1', 'media_type' => 'IMAGE', 'media_url' => 'https://scontent.cdninstagram.com/a.jpg'],
                ['id' => '222-2', 'media_type' => 'VIDEO', 'media_url' => 'https://scontent.cdninstagram.com/b.mp4', 'thumbnail_url' => 'https://scontent.cdninstagram.com/b.jpg'],
            ],
        ],
    ]);

    $reels = $normalizer->media([
        'id' => '333',
        'media_type' => 'VIDEO',
        'media_product_type' => 'REELS',
        'media_url' => 'https://scontent.cdninstagram.com/reel.mp4',
        'thumbnail_url' => 'https://scontent.cdninstagram.com/reel.jpg',
        'permalink' => 'https://www.instagram.com/reel/ghi/',
        'timestamp' => '2026-09-01T10:00:00+0000',
    ]);

    expect($image['media_type'])->toBe(InstagramMediaType::Image->value)
        ->and($carousel['media_type'])->toBe(InstagramMediaType::CarouselAlbum->value)
        ->and($carousel['children'])->toHaveCount(2)
        ->and($carousel['children'][1]['sort_order'])->toBe(1)
        ->and($reels['media_type'])->toBe(InstagramMediaType::Reels->value)
        ->and($image['permalink'])->toStartWith('https://');
});

it('normalizes stories with expiration from timestamp', function () {
    $normalizer = new InstagramResponseNormalizer;
    $story = $normalizer->media([
        'id' => '444',
        'media_type' => 'IMAGE',
        'media_url' => 'https://scontent.cdninstagram.com/story.jpg',
        'timestamp' => '2026-09-07T01:00:00+0000',
    ], true);

    expect($story['is_story'])->toBeTrue()
        ->and($story['expires_at'])->not->toBeNull()
        ->and($story['expires_at']->greaterThan($story['published_at']))->toBeTrue();
});

it('rejects javascript urls and sanitizes captions without html', function () {
    $normalizer = new InstagramResponseNormalizer;
    $item = $normalizer->media([
        'id' => '555',
        'caption' => '<script>alert(1)</script> Progress review on critical path activities.',
        'media_type' => 'IMAGE',
        'media_url' => 'javascript:alert(1)',
        'permalink' => 'https://www.instagram.com/p/safe/',
    ]);

    $caption = InstagramCaptionSanitizer::text($item['caption']);

    expect($item['media_url'])->toBeNull()
        ->and($caption)->not->toContain('<script>')
        ->and($caption)->toContain('Progress review');
});

it('redacts tokens from log context', function () {
    config()->set('instagram.access_token', 'IGSECRETTOKEN123');
    $logger = new InstagramLogger;
    $safe = $logger->safe([
        'url' => 'https://graph.instagram.com/me?access_token=IGSECRETTOKEN123',
        'authorization' => 'Bearer IGSECRETTOKEN123',
    ]);

    $encoded = json_encode($safe);

    expect($encoded)->not->toContain('IGSECRETTOKEN123')
        ->and($logger->containsSecret($encoded))->toBeFalse();
});

it('classifies authentication errors as non-retryable', function () {
    expect(InstagramErrorClass::Authentication->retryable())->toBeFalse()
        ->and(InstagramErrorClass::Timeout->retryable())->toBeTrue()
        ->and(InstagramErrorClass::RateLimit->retryable())->toBeFalse();
});
