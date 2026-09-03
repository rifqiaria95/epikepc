<?php

use App\Enums\Career\CareerTokenPurpose;
use App\Enums\Career\DocumentType;
use App\Enums\Career\QuestionType;
use App\Enums\Career\VacancyStatus;
use App\Models\Career\Candidate;
use App\Models\Career\CareerAccessToken;
use App\Models\Career\JobApplication;
use App\Models\Career\JobVacancy;
use App\Models\User;
use App\Notifications\Career\ApplicationSubmittedNotification;
use App\Notifications\Career\VerifyApplicationNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function careerPdf(?string $name = 'cv.pdf'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, '%PDF-1.4 testdocument');
}

function publishedVacancy(array $overrides = []): JobVacancy
{
    return JobVacancy::factory()->published()->create($overrides);
}

function applyPayload(array $overrides = []): array
{
    return array_merge([
        'full_name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
        'domicile_city' => 'Jakarta',
        'domicile_province' => 'DKI Jakarta',
        'highest_education' => 'S1',
        'education_major' => 'Teknik Sipil',
        'total_experience_years' => 4,
        'availability_type' => 'IMMEDIATELY',
        'privacy_consent' => 1,
        'accuracy_declaration' => 1,
        'cv' => careerPdf(),
    ], $overrides);
}

it('lists only published active vacancies', function () {
    $visible = publishedVacancy(['title' => 'Visible Engineer', 'slug' => 'visible-engineer']);
    JobVacancy::factory()->create(['status' => VacancyStatus::Draft, 'slug' => 'draft-role']);
    JobVacancy::factory()->closed()->create(['slug' => 'closed-role']);
    JobVacancy::factory()->archived()->create(['slug' => 'archived-role']);
    JobVacancy::factory()->future()->create(['slug' => 'future-role']);
    JobVacancy::factory()->expired()->create(['slug' => 'expired-role']);

    $this->get(route('frontend.careers.index'))
        ->assertOk()
        ->assertSee('Visible Engineer')
        ->assertDontSee('draft-role')
        ->assertDontSee('closed-role')
        ->assertDontSee('archived-role')
        ->assertDontSee('future-role')
        ->assertDontSee('expired-role');

    expect($visible->acceptsApplications())->toBeTrue();
});

it('shows vacancy detail by slug', function () {
    $vacancy = publishedVacancy(['title' => 'Project Engineer', 'slug' => 'project-engineer']);

    $this->get(route('frontend.careers.show', $vacancy->slug))
        ->assertOk()
        ->assertSee('Project Engineer')
        ->assertSee('Lamar sekarang');
});

it('hides apply button and rejects apply for closed vacancy', function () {
    $vacancy = JobVacancy::factory()->closed()->create(['slug' => 'closed-qs', 'title' => 'Closed QS']);

    $this->get(route('frontend.careers.show', $vacancy->slug))->assertNotFound();

    $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())
        ->assertStatus(409);
});

it('creates an application without login and without a cms user', function () {
    Notification::fake();
    Storage::fake('local');
    $beforeUsers = User::query()->count();
    $vacancy = publishedVacancy(['slug' => 'open-pe']);

    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())
        ->assertRedirect(route('frontend.careers.received', $vacancy->slug));

    expect(JobApplication::query()->count())->toBe(1)
        ->and(User::query()->count())->toBe($beforeUsers)
        ->and(JobApplication::query()->first()->status->value)->toBe('PENDING_VERIFICATION');

    $document = JobApplication::query()->first()->documents()->first();
    expect($document->disk)->toBe('local')
        ->and($document->document_type)->toBe(DocumentType::Cv)
        ->and(Storage::disk('local')->exists($document->path))->toBeTrue();
});

it('deduplicates candidates and rejects a second application to the same vacancy', function () {
    Notification::fake();
    Storage::fake('local');
    $first = publishedVacancy(['slug' => 'role-a', 'title' => 'Role A']);
    $second = publishedVacancy(['slug' => 'role-b', 'title' => 'Role B']);

    $this->post(route('frontend.careers.apply.store', $first->slug), applyPayload())->assertRedirect();
    $this->post(route('frontend.careers.apply.store', $second->slug), applyPayload(['full_name' => 'Budi S']))->assertRedirect();
    $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('frontend.careers.apply.store', $first->slug), applyPayload())
        ->assertStatus(409)
        ->assertJsonFragment(['message' => 'Anda sudah pernah melamar lowongan ini.']);

    expect(Candidate::query()->count())->toBe(1)
        ->and(JobApplication::query()->count())->toBe(2);
});

it('rejects invalid and oversized cv files', function () {
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'cv-rules']);

    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload([
        'cv' => UploadedFile::fake()->createWithContent('notes.txt', 'not a cv'),
    ]))->assertSessionHasErrors('cv');

    config(['career.documents.max_cv_kilobytes' => 1]);
    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload([
        'cv' => UploadedFile::fake()->createWithContent('huge.pdf', '%PDF-1.4 '.str_repeat('A', 3000)),
    ]))->assertSessionHasErrors('cv');
});

it('stores a hashed verification token and submits after a valid one-time verify', function () {
    Notification::fake();
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'verify-me']);

    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())->assertRedirect();

    $plain = null;
    Notification::assertSentOnDemand(VerifyApplicationNotification::class, function (VerifyApplicationNotification $notification) use (&$plain) {
        $plain = $notification->plaintextToken;

        return true;
    });

    $token = CareerAccessToken::query()->first();
    expect($token->token_hash)->toBe(hash('sha256', $plain))
        ->and($token->token_hash)->not->toBe($plain)
        ->and($token->purpose)->toBe(CareerTokenPurpose::EmailVerification);

    $this->get(route('frontend.careers.verify', $plain))
        ->assertOk()
        ->assertSee('Lamaran terkirim')
        ->assertSee(JobApplication::query()->first()->reference_number);

    expect(JobApplication::query()->first()->status->value)->toBe('SUBMITTED');

    $this->get(route('frontend.careers.verify', $plain))->assertStatus(410);
});

it('rejects an expired verification token safely', function () {
    Notification::fake();
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'expired-token']);
    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())->assertRedirect();

    $plain = null;
    Notification::assertSentOnDemand(VerifyApplicationNotification::class, function ($n) use (&$plain) {
        $plain = $n->plaintextToken;

        return true;
    });

    CareerAccessToken::query()->update(['expires_at' => now()->subMinute()]);

    $this->get(route('frontend.careers.verify', $plain))
        ->assertStatus(410)
        ->assertSee('kedaluwarsa');
});

it('throttles verification resend', function () {
    Notification::fake();
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'resend-me']);
    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())->assertRedirect();

    $this->postJson(route('frontend.careers.resend', $vacancy->slug), ['email' => 'budi@example.com'])->assertOk();
    $this->postJson(route('frontend.careers.resend', $vacancy->slug), ['email' => 'budi@example.com'])->assertStatus(429);
});

it('validates required dynamic answers', function () {
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'with-q']);
    $question = $vacancy->questions()->create([
        'question' => 'Bersedia dinas luar kota?',
        'type' => QuestionType::Boolean,
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())
        ->assertSessionHasErrors('answers.'.$question->id);
});

it('does not expose internal fields on the public status page', function () {
    Notification::fake();
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'status-page']);
    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())->assertRedirect();

    $plain = null;
    Notification::assertSentOnDemand(VerifyApplicationNotification::class, function ($n) use (&$plain) {
        $plain = $n->plaintextToken;

        return true;
    });

    $this->get(route('frontend.careers.verify', $plain))->assertOk();

    $statusPlain = CareerAccessToken::query()
        ->where('purpose', CareerTokenPurpose::StatusAccess->value)
        ->latest()
        ->first();

    $application = JobApplication::query()->first();
    $application->notes()->create([
        'note' => 'RAHASIA_INTERNAL',
        'created_by' => User::factory()->create()->id,
    ]);

    $hash = $statusPlain->token_hash;
    // Recover plaintext from submitted notification instead of hash.
    $statusToken = null;
    Notification::assertSentOnDemand(
        ApplicationSubmittedNotification::class,
        function ($n) use (&$statusToken) {
            $statusToken = $n->statusPlaintextToken;

            return true;
        }
    );

    $this->get(route('frontend.careers.status', $statusToken))
        ->assertOk()
        ->assertSee($application->reference_number)
        ->assertDontSee('RAHASIA_INTERNAL')
        ->assertDontSee($application->id)
        ->assertDontSee('storage/');
});

it('does not expose private documents via public storage url', function () {
    Notification::fake();
    Storage::fake('local');
    $vacancy = publishedVacancy(['slug' => 'private-doc']);
    $this->post(route('frontend.careers.apply.store', $vacancy->slug), applyPayload())->assertRedirect();
    $path = JobApplication::query()->first()->documents()->first()->path;

    $this->get('/storage/'.$path)->assertStatus(403);
});
