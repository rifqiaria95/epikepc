<?php

namespace Database\Factories;

use App\Enums\InstagramMediaType;
use App\Models\InstagramMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstagramMedia>
 */
class InstagramMediaFactory extends Factory
{
    protected $model = InstagramMedia::class;

    public function definition(): array
    {
        return [
            'external_media_id' => (string) fake()->unique()->numerify('178##########'),
            'media_type' => InstagramMediaType::Image,
            'caption' => fake()->sentence(),
            'media_url' => 'https://scontent.cdninstagram.com/'.fake()->uuid().'.jpg',
            'thumbnail_url' => 'https://scontent.cdninstagram.com/'.fake()->uuid().'-thumb.jpg',
            'permalink' => 'https://www.instagram.com/p/'.fake()->regexify('[A-Za-z0-9]{11}').'/',
            'published_at' => now()->subHours(fake()->numberBetween(1, 48)),
            'expires_at' => null,
            'is_story' => false,
            'is_visible' => true,
            'sort_order' => fake()->numberBetween(0, 20),
            'like_count' => null,
            'comments_count' => null,
            'synced_at' => now(),
        ];
    }

    public function story(): static
    {
        return $this->state(fn () => [
            'is_story' => true,
            'expires_at' => now()->addHours(12),
            'published_at' => now()->subHours(2),
        ]);
    }

    public function expiredStory(): static
    {
        return $this->state(fn () => [
            'is_story' => true,
            'expires_at' => now()->subHour(),
            'published_at' => now()->subHours(30),
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn () => [
            'is_visible' => false,
        ]);
    }

    public function carousel(): static
    {
        return $this->state(fn () => [
            'media_type' => InstagramMediaType::CarouselAlbum,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'media_type' => InstagramMediaType::Video,
        ]);
    }

    public function reels(): static
    {
        return $this->state(fn () => [
            'media_type' => InstagramMediaType::Reels,
        ]);
    }
}
