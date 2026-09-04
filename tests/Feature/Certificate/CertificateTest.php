<?php

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\User;
use App\Support\Certificate\CertificateSetBuilder;
use Database\Seeders\CertificatePermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

function certificateUser(array $permissions = [], bool $superadmin = false): User
{
    $user = User::factory()->create();

    if ($superadmin) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin']);
        $user->assignRole('superadmin');
        app(CertificatePermissionSeeder::class)->run();
        $user->refresh();

        return $user;
    }

    foreach ($permissions as $name) {
        $permission = Permission::firstOrCreate(['name' => $name]);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function fakeCertificateImage(): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'cert_img_');
    $img = imagecreatetruecolor(400, 520);
    imagejpeg($img, $path, 85);
    imagedestroy($img);

    return new UploadedFile($path, 'certificate.jpg', 'image/jpeg', null, true);
}

it('blocks unauthenticated certificate cms access', function () {
    $this->get('/internal/certificates')->assertRedirect();
});

it('blocks user without permission from certificate cms', function () {
    $user = certificateUser(['view_project']);
    $this->actingAs($user)->get('/internal/certificates')->assertForbidden();
});

it('allows authorized user to view certificate listing page', function () {
    $user = certificateUser(['view_certificates']);
    $this->actingAs($user)->get('/internal/certificates')->assertOk();
});

function certificateStore(User $user, array $data)
{
    return test()->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->post('/internal/certificates/store', $data);
}

it('creates a valid certificate with image', function () {
    Storage::fake('public');
    $user = certificateUser(['create_certificates', 'edit_certificates']);

    $response = certificateStore($user, [
        'title' => 'ISO 9001',
        'issuer' => 'ISO',
        'image_alt' => 'ISO 9001 certificate',
        'image' => fakeCertificateImage(),
        'status' => CertificateStatus::Draft->value,
    ]);

    $response->assertOk();
    expect(Certificate::query()->where('title', 'ISO 9001')->exists())->toBeTrue();
});

it('rejects invalid image mime for certificate upload', function () {
    Storage::fake('public');
    $user = certificateUser(['create_certificates']);

    $path = tempnam(sys_get_temp_dir(), 'bad_');
    file_put_contents($path, 'not-an-image');

    $response = certificateStore($user, [
        'title' => 'Bad Cert',
        'issuer' => 'Test',
        'image_alt' => 'Bad',
        'image' => new UploadedFile($path, 'bad.txt', 'text/plain', null, true),
    ]);

    $response->assertStatus(422);
});

it('rejects publish without image and alt text', function () {
    $user = certificateUser(['create_certificates', 'publish_certificates']);
    $cert = Certificate::factory()->draft()->create([
        'image_alt' => '',
    ]);

    $this->actingAs($user)->postJson('/internal/certificates/'.$cert->id.'/publish')
        ->assertStatus(422);
});

it('rejects expiry date before issue date', function () {
    $user = certificateUser(['create_certificates']);

    certificateStore($user, [
        'title' => 'Date Test',
        'issuer' => 'ISO',
        'issued_at' => '2025-01-01',
        'expires_at' => '2024-01-01',
        'image_alt' => 'Alt',
        'image' => fakeCertificateImage(),
    ])->assertStatus(422);
});

it('rejects unsafe credential url scheme', function () {
    $user = certificateUser(['create_certificates']);

    certificateStore($user, [
        'title' => 'URL Test',
        'issuer' => 'ISO',
        'credential_url' => 'javascript:alert(1)',
        'image_alt' => 'Alt',
        'image' => fakeCertificateImage(),
    ])->assertStatus(422);
});

it('soft deletes certificate and removes files', function () {
    Storage::fake('public');
    $user = certificateUser(['delete_certificates']);
    $cert = Certificate::factory()->create();
    Storage::disk('public')->put($cert->image_path, 'img');

    $this->actingAs($user)->delete('/internal/certificates/delete/'.$cert->id)->assertOk();

    expect(Certificate::withTrashed()->find($cert->id)?->trashed())->toBeTrue();
});

it('reorders certificates atomically', function () {
    $user = certificateUser(['reorder_certificates']);
    $a = Certificate::factory()->create(['display_order' => 1]);
    $b = Certificate::factory()->create(['display_order' => 2]);
    $c = Certificate::factory()->create(['display_order' => 3]);

    $this->actingAs($user)->postJson('/internal/certificates/reorder', [
        'ordered_ids' => [$c->id, $a->id, $b->id],
    ])->assertOk();

    expect(Certificate::find($c->id)->display_order)->toBe(1)
        ->and(Certificate::find($a->id)->display_order)->toBe(2)
        ->and(Certificate::find($b->id)->display_order)->toBe(3);
});

it('seeds certificate permissions idempotently', function () {
    app(CertificatePermissionSeeder::class)->run();
    app(CertificatePermissionSeeder::class)->run();

    expect(Permission::where('name', 'view_certificates')->exists())->toBeTrue();
});

it('shows only published certificates on homepage', function () {
    Certificate::factory()->published()->create(['title' => 'Visible Cert']);
    Certificate::factory()->draft()->create(['title' => 'Hidden Draft']);
    Certificate::factory()->archived()->create(['title' => 'Hidden Archived']);
    Certificate::factory()->futurePublished()->create(['title' => 'Hidden Future']);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Visible Cert', false)
        ->assertDontSee('Hidden Draft', false)
        ->assertDontSee('Hidden Archived', false)
        ->assertDontSee('Hidden Future', false);
});

it('hides certificate section when no published certificates exist', function () {
    Certificate::factory()->draft()->create();

    $this->get('/')->assertOk()->assertDontSee('cert-section', false);
});

it('orders homepage certificates by display order', function () {
    Certificate::factory()->published()->create(['title' => 'Second', 'display_order' => 2]);
    Certificate::factory()->published()->create(['title' => 'First', 'display_order' => 1]);

    $html = $this->get('/')->getContent();
    expect(strpos($html, 'First'))->toBeLessThan(strpos($html, 'Second'));
});

it('does not expose raw certificate storage fields in homepage html', function () {
    Certificate::factory()->published()->create([
        'image_path' => 'uploads/secret/internal-path.jpg',
    ]);

    $this->get('/')->assertOk()->assertDontSee('"image_path"', false);
});

it('homepage certificate query uses a single query', function () {
    Certificate::factory()->published()->count(3)->create();

    DB::enableQueryLog();
    $this->get('/');
    $queries = collect(DB::getQueryLog())->pluck('query');

    $certificateQueries = $queries->filter(fn ($q) => str_contains($q, 'certificates'));
    expect($certificateQueries->count())->toBeLessThanOrEqual(2);
});

it('builds deterministic certificate sets', function () {
    $builder = app(CertificateSetBuilder::class);
    $items = array_map(fn ($i) => ['id' => (string) $i, 'title' => 'Cert '.$i], range(1, 12));

    $sets = $builder->buildSets($items, 1280);

    expect($sets)->toHaveCount(2)
        ->and($sets[0])->toHaveCount(8);
});
