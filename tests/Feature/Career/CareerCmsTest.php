<?php

use App\Enums\Career\ApplicationStatus;
use App\Models\Career\JobApplication;
use App\Models\Career\JobApplicationDocument;
use App\Models\Career\JobVacancy;
use App\Models\User;
use App\Services\Career\JobApplicationTransitionService;
use Database\Seeders\CareerPermissionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function careerUser(array $permissions = [], bool $superadmin = false): User
{
    $user = User::factory()->create();

    if ($superadmin) {
        Role::firstOrCreate(['name' => 'superadmin']);
        $user->assignRole('superadmin');
        app(CareerPermissionSeeder::class)->run();
        $user->refresh();

        return $user;
    }

    foreach ($permissions as $name) {
        $permission = Permission::firstOrCreate(['name' => $name]);
        $user->givePermissionTo($permission);
    }

    return $user;
}

it('blocks unauthenticated career cms access', function () {
    $this->get('/internal/career')->assertRedirect();
    $this->get('/internal/career/applications')->assertRedirect();
});

it('blocks unauthorized roles from applications and documents', function () {
    $user = careerUser(['view_project']);
    $application = JobApplication::factory()->submitted()->create();
    $document = JobApplicationDocument::query()->create([
        'job_application_id' => $application->id,
        'document_type' => 'CV',
        'original_name' => 'cv.pdf',
        'stored_name' => 'cv.pdf',
        'disk' => 'local',
        'path' => 'career/documents/cv.pdf',
        'mime_type' => 'application/pdf',
        'size' => 100,
        'checksum' => hash('sha256', 'x'),
        'scan_status' => 'PENDING',
        'uploaded_at' => now(),
    ]);

    $this->actingAs($user)->get('/internal/career/applications')->assertForbidden();
    $this->actingAs($user)->get(route('career.applications.documents.download', [$application->id, $document->id]))->assertForbidden();
});

it('allows a permitted recruiter to view applications and download documents', function () {
    Storage::fake('local');
    Storage::disk('local')->put('career/documents/cv.pdf', '%PDF-1.4 ok');

    $user = careerUser([
        'view_applications',
        'view_candidate_documents',
        'download_candidate_documents',
    ]);
    $application = JobApplication::factory()->submitted()->create();
    $document = JobApplicationDocument::query()->create([
        'job_application_id' => $application->id,
        'document_type' => 'CV',
        'original_name' => 'cv.pdf',
        'stored_name' => 'cv.pdf',
        'disk' => 'local',
        'path' => 'career/documents/cv.pdf',
        'mime_type' => 'application/pdf',
        'size' => 100,
        'checksum' => hash('sha256', 'x'),
        'scan_status' => 'PENDING',
        'uploaded_at' => now(),
    ]);

    $this->actingAs($user)->get(route('career.applications.show', $application->id))->assertOk();
    $this->actingAs($user)
        ->get(route('career.applications.documents.download', [$application->id, $document->id]))
        ->assertOk()
        ->assertHeader('content-disposition');
});

it('enforces vacancy publish validation and permissions', function () {
    $viewer = careerUser(['view_vacancies']);
    $editor = careerUser(['view_vacancies', 'create_vacancies', 'edit_vacancies', 'publish_vacancies']);

    $this->actingAs($viewer)->postJson('/internal/career/vacancies/store', [
        'title' => 'X',
    ])->assertForbidden();

    $vacancy = JobVacancy::factory()->create([
        'title' => '',
        'summary' => '',
        'description' => '',
        'responsibilities' => '',
        'qualifications' => '',
    ]);

    $this->actingAs($editor)
        ->postJson('/internal/career/vacancies/'.$vacancy->id.'/publish')
        ->assertStatus(422);
});

it('rejects direct status mass assignment and invalid transitions', function () {
    Notification::fake();
    $user = careerUser(['view_applications', 'change_application_status', 'reject_applications']);
    $application = JobApplication::factory()->submitted()->create();

    $this->actingAs($user)->putJson('/internal/career/applications/'.$application->id, [
        'status' => 'HIRED',
    ])->assertStatus(405);

    $this->actingAs($user)->postJson(route('career.applications.transition', $application->id), [
        'to_status' => 'HIRED',
    ])->assertStatus(422);

    $this->actingAs($user)->postJson(route('career.applications.transition', $application->id), [
        'to_status' => 'SCREENING',
    ])->assertOk();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Screening)
        ->and($application->statusHistories()->count())->toBe(1);
});

it('appends exactly one history record per allowed transition', function () {
    Notification::fake();
    $service = app(JobApplicationTransitionService::class);
    $application = JobApplication::factory()->submitted()->create();
    $user = careerUser(['change_application_status']);

    $service->transition($application, ApplicationStatus::Screening, $user, [
        'public_message' => 'Masuk screening',
    ]);
    $service->transition($application->fresh(), ApplicationStatus::Screening, $user);

    expect($application->statusHistories()->count())->toBe(1);
});

it('does not send after-commit notifications when the transition rolls back', function () {
    Notification::fake();
    $application = JobApplication::factory()->submitted()->create();
    $user = careerUser(['change_application_status']);

    try {
        DB::transaction(function () use ($application, $user) {
            app(JobApplicationTransitionService::class)->transition(
                $application,
                ApplicationStatus::Screening,
                $user,
                ['public_message' => 'test']
            );
            throw new RuntimeException('force rollback');
        });
    } catch (RuntimeException) {
    }

    expect($application->fresh()->status)->toBe(ApplicationStatus::Submitted);
    Notification::assertNothingSent();
});
