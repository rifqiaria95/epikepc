<?php

namespace Database\Factories;

use App\Enums\InstagramMediaType;
use App\Enums\InstagramPostStatus;
use App\Models\InstagramPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstagramPost>
 */
class InstagramPostFactory extends Factory
{
    protected $model = InstagramPost::class;

    public function definition(): array
    {
        return [
            'media_type' => InstagramMediaType::Image,
            'caption' => fake()->sentence(),
            'media_path' => 'uploads/instagram/posts/example.jpg',
            'media_url' => 'https://example.com/instagram/example.jpg',
            'status' => InstagramPostStatus::Scheduled,
            'scheduled_at' => now()->addHour(),
            'published_at' => null,
            'container_id' => null,
            'external_media_id' => null,
            'last_error_class' => null,
            'last_error_message' => null,
            'created_by' => null,
        ];
    }

    public function due(): static
    {
        return $this->state(fn () => [
            'status' => InstagramPostStatus::Scheduled,
            'scheduled_at' => now()->subMinute(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => InstagramPostStatus::Published,
            'scheduled_at' => now()->subHour(),
            'published_at' => now()->subMinutes(30),
            'external_media_id' => (string) fake()->numerify('178##########'),
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'media_type' => InstagramMediaType::Reels,
            'media_path' => 'uploads/instagram/posts/example.mp4',
            'media_url' => 'https://example.com/instagram/example.mp4',
        ]);
    }
}
