<?php

namespace Database\Factories;

use App\Enums\InstagramMediaType;
use App\Models\InstagramMedia;
use App\Models\InstagramMediaChild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstagramMediaChild>
 */
class InstagramMediaChildFactory extends Factory
{
    protected $model = InstagramMediaChild::class;

    public function definition(): array
    {
        return [
            'instagram_media_id' => InstagramMedia::factory()->carousel(),
            'external_media_id' => (string) fake()->unique()->numerify('179##########'),
            'media_type' => InstagramMediaType::Image,
            'media_url' => 'https://scontent.cdninstagram.com/'.fake()->uuid().'.jpg',
            'thumbnail_url' => 'https://scontent.cdninstagram.com/'.fake()->uuid().'-thumb.jpg',
            'sort_order' => 0,
        ];
    }
}
