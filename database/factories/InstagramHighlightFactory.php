<?php

namespace Database\Factories;

use App\Models\InstagramHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstagramHighlight>
 */
class InstagramHighlightFactory extends Factory
{
    protected $model = InstagramHighlight::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['Projects', 'On Site', 'Safety', 'Our Team', 'Events']),
            'cover_url' => '/storage/seed/gallery/image66.png',
            'permalink' => 'https://www.instagram.com/stories/highlights/'.fake()->numerify('##############').'/',
            'is_visible' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn () => [
            'is_visible' => false,
        ]);
    }
}
