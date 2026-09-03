<?php

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\VacancyStatus;
use App\Models\Career\Candidate;
use App\Models\Career\JobApplication;
use App\Models\Career\JobVacancy;
use App\Models\User;
use App\Queries\Career\CareerSummaryQuery;
use Spatie\Permission\Models\Permission;

function careerSummaryUser(array $permissions = []): User
{
    $user = User::factory()->create();

    foreach ($permissions as $name) {
        $permission = Permission::firstOrCreate(['name' => $name]);
        $user->givePermissionTo($permission);
    }

    return $user;
}

it('aggregates vacancy summary metrics in a single query', function () {
    JobVacancy::factory()->create(['status' => VacancyStatus::Draft]);
    JobVacancy::factory()->published()->create(['closes_at' => now()->addDays(3)]);
    JobVacancy::factory()->published()->create(['closes_at' => now()->addMonth()]);
    JobVacancy::factory()->create(['status' => VacancyStatus::Closed]);

    $metrics = app(CareerSummaryQuery::class)->vacancyMetrics();

    expect($metrics['total'])->toBe(4)
        ->and($metrics['draft'])->toBe(1)
        ->and($metrics['active'])->toBe(2)
        ->and($metrics['closing_soon'])->toBe(1)
        ->and($metrics['closed'])->toBe(1);
});

it('aggregates application summary metrics without loading models', function () {
    JobApplication::factory()->create(['status' => ApplicationStatus::PendingVerification]);
    JobApplication::factory()->submitted()->create(['status' => ApplicationStatus::Screening]);
    JobApplication::factory()->submitted()->create(['status' => ApplicationStatus::Interview]);
    JobApplication::factory()->submitted()->create(['status' => ApplicationStatus::Hired]);

    $metrics = app(CareerSummaryQuery::class)->applicationMetrics();

    expect($metrics['total'])->toBe(4)
        ->and($metrics['pending_verification'])->toBe(1)
        ->and($metrics['in_pipeline'])->toBe(2)
        ->and($metrics['hired'])->toBe(1);
});

it('aggregates candidate summary metrics with subqueries', function () {
    $repeat = Candidate::factory()->create();
    $single = Candidate::factory()->create(['created_at' => now()->subDays(10)]);

    JobApplication::factory()->submitted()->create(['candidate_id' => $repeat->id]);
    JobApplication::factory()->submitted()->create(['candidate_id' => $repeat->id]);
    JobApplication::factory()->submitted()->create([
        'candidate_id' => $single->id,
        'status' => ApplicationStatus::Hired,
    ]);

    $metrics = app(CareerSummaryQuery::class)->candidateMetrics();

    expect($metrics['total'])->toBe(2)
        ->and($metrics['repeat_applicants'])->toBe(1)
        ->and($metrics['with_applications'])->toBe(2)
        ->and($metrics['with_active_applications'])->toBe(1);
});

it('presents stat cards for cms listing pages', function () {
    $cards = app(CareerSummaryQuery::class)->vacancyStatCards();

    expect($cards)->toHaveCount(4)
        ->and($cards[0])->toHaveKeys(['label', 'value', 'icon', 'color']);
});

it('renders summary cards on career cms listing pages', function () {
    $user = careerSummaryUser(['view_vacancies', 'view_applications', 'view_candidates']);

    $this->actingAs($user)
        ->get('/internal/career/vacancies')
        ->assertOk()
        ->assertSee('Total Lowongan');

    $this->actingAs($user)
        ->get('/internal/career/applications')
        ->assertOk()
        ->assertSee('Total Lamaran');

    $this->actingAs($user)
        ->get('/internal/career/candidates')
        ->assertOk()
        ->assertSee('Total Kandidat');
});
