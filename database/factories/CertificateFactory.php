<?php

namespace Database\Factories;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'issuer' => fake()->company(),
            'description' => fake()->optional()->paragraph(),
            'certificate_number' => fake()->optional()->bothify('CERT-####-??'),
            'issued_at' => fake()->optional()->dateTimeBetween('-3 years', 'now'),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+2 years'),
            'credential_url' => fake()->optional()->url(),
            'image_path' => 'uploads/2026/03/certificate/images/'.Str::random(40).'.jpg',
            'thumbnail_path' => null,
            'image_alt' => fake()->sentence(4),
            'status' => CertificateStatus::Draft,
            'is_featured' => false,
            'display_order' => fake()->numberBetween(1, 100),
            'published_at' => null,
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => CertificateStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => CertificateStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => CertificateStatus::Archived,
        ]);
    }

    public function futurePublished(): static
    {
        return $this->state(fn () => [
            'status' => CertificateStatus::Published,
            'published_at' => now()->addWeek(),
        ]);
    }
}
