<?php

namespace Database\Factories\Career;

use App\Models\Career\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();

        return [
            'full_name' => fake()->name(),
            'email' => $email,
            'normalized_email' => Candidate::normalizeEmail($email),
            'phone' => '08123456789',
            'normalized_phone' => '628123456789',
            'domicile_city' => 'Bandung',
            'domicile_province' => 'Jawa Barat',
            'highest_education' => 'S1',
            'education_major' => 'Teknik Sipil',
            'total_experience_years' => 3,
        ];
    }
}
