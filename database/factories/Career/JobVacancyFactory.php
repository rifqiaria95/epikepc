<?php

namespace Database\Factories\Career;

use App\Enums\Career\EmploymentType;
use App\Enums\Career\ExperienceLevel;
use App\Enums\Career\VacancyStatus;
use App\Enums\Career\WorkArrangement;
use App\Models\Career\JobVacancy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobVacancy>
 */
class JobVacancyFactory extends Factory
{
    protected $model = JobVacancy::class;

    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'code' => 'EPC-'.fake()->unique()->numerify('######'),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'department' => fake()->randomElement(['Engineering', 'Construction', 'HSSE', 'Finance']),
            'location_city' => 'Jakarta',
            'location_province' => 'DKI Jakarta',
            'employment_type' => EmploymentType::FullTime,
            'work_arrangement' => WorkArrangement::Onsite,
            'experience_level' => ExperienceLevel::Mid,
            'summary' => fake()->sentence(12),
            'description' => '<p>'.fake()->paragraph().'</p>',
            'responsibilities' => '<ul><li>'.fake()->sentence().'</li></ul>',
            'qualifications' => '<ul><li>'.fake()->sentence().'</li></ul>',
            'headcount' => 1,
            'requires_site_travel' => false,
            'allows_salary_expectation' => false,
            'status' => VacancyStatus::Draft,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => VacancyStatus::Published,
            'published_at' => now()->subDay(),
            'closes_at' => now()->addMonth(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => VacancyStatus::Closed,
            'published_at' => now()->subMonth(),
            'closes_at' => now()->subDay(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => VacancyStatus::Archived,
            'published_at' => now()->subMonths(2),
            'closes_at' => now()->subMonth(),
        ]);
    }

    public function future(): static
    {
        return $this->state(fn () => [
            'status' => VacancyStatus::Published,
            'published_at' => now()->addWeek(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => VacancyStatus::Published,
            'published_at' => now()->subMonth(),
            'closes_at' => now()->subDay(),
        ]);
    }
}
