<?php

use App\Models\InstagramHighlight;
use App\Models\InstagramMedia;
use App\Models\InstagramMediaChild;
use App\Models\InstagramSetting;
use App\Models\User;
use App\Services\Instagram\InstagramFeedService;
use App\Services\Instagram\InstagramSyncService;
use Database\Seeders\InstagramPermissionSeeder;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function instagramUser(array $permissions = [], bool $superadmin = false): User
{
    $user = User::factory()->create();

    if ($superadmin) {
        Role::firstOrCreate(['name' => 'superadmin']);
        $user->assignRole('superadmin');
        app(InstagramPermissionSeeder::class)->run();
        $user->refresh();

        return $user;
    }

    foreach ($permissions as $name) {
        $permission = Permission::firstOrCreate(['name' => $name]);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function fakeInstagramConfig(): void
{
    config()->set('instagram.enabled', true);
    config()->set('instagram.user_id', '17841400000000000');
    config()->set('instagram.access_token', 'TEST_TOKEN_NOT_FOR_PRODUCTION');
    config()->set('instagram.api_base_url', 'https://graph.instagram.com');
    config()->set('instagram.api_version', 'v21.0');
    config()->set('instagram.feed_limit', 6);
    config()->set('instagram.story_limit', 10);
}

it('returns 404 for removed public news routes', function () {
    $this->get('/news')->assertNotFound();
    $this->get('/news/some-slug')->assertNotFound();
});

it('returns 404 for removed cms news routes', function () {
    $user = instagramUser(['view_instagram']);
    $this->actingAs($user)->get('/internal/news')->assertNotFound();
    $this->actingAs($user)->get('/internal/news/kategori')->assertNotFound();
    $this->actingAs($user)->get('/internal/news/tag')->assertNotFound();
});

it('does not render news navigation or query news on the homepage', function () {
    config()->set('instagram.enabled', false);

    DB::enableQueryLog();
    $response = $this->get('/');
    $queries = collect(DB::getQueryLog())->pluck('query')->implode(' ');

    $response->assertOk()
        ->assertDontSee('News &amp; Media', false)
        ->assertDontSee('View All News', false)
        ->assertDontSee('href="/news"', false);

    expect($queries)->not->toContain(' from "news"')
        ->and($queries)->not->toContain(' from news');
});

it('hides the instagram section when disabled', function () {
    config()->set('instagram.enabled', true);
    InstagramSetting::current()->update(['enabled' => false]);
    InstagramMedia::factory()->count(2)->create();

    $this->get('/')->assertOk()->assertDontSee('id="instagram"', false);
});

it('shows at most six visible feed items and hides expired or hidden media', function () {
    fakeInstagramConfig();
    InstagramSetting::current()->update(['enabled' => true, 'feed_limit' => 6]);

    InstagramMedia::factory()->count(8)->create(['is_visible' => true]);
    InstagramMedia::factory()->hidden()->create();
    InstagramMedia::factory()->expiredStory()->create();
    InstagramMedia::factory()->story()->create();

    $widget = app(InstagramFeedService::class)->buildWidget();

    expect($widget['feed'])->toHaveCount(6)
        ->and($widget['stories'])->toHaveCount(1)
        ->and(collect($widget['feed'])->pluck('caption')->implode(' '))->not->toContain('<script>');
});

it('eager loads carousel children without n+1 queries', function () {
    fakeInstagramConfig();
    InstagramSetting::current()->update(['enabled' => true]);

    $parents = InstagramMedia::factory()->carousel()->count(3)->create();
    foreach ($parents as $index => $parent) {
        InstagramMediaChild::factory()->count(2)->create([
            'instagram_media_id' => $parent->id,
            'sort_order' => $index,
        ]);
    }

    DB::enableQueryLog();
    app(InstagramFeedService::class)->buildWidget();
    $mediaQueries = collect(DB::getQueryLog())->filter(
        fn ($q) => str_contains($q['query'], 'instagram_media')
    );

    expect($mediaQueries->count())->toBeLessThanOrEqual(3);
});

it('upserts duplicate sync payloads without duplicating rows', function () {
    $sync = app(InstagramSyncService::class);
    $payload = [[
        'external_media_id' => 'dup-1',
        'media_type' => 'IMAGE',
        'caption' => 'One',
        'media_url' => 'https://scontent.cdninstagram.com/one.jpg',
        'thumbnail_url' => 'https://scontent.cdninstagram.com/one.jpg',
        'permalink' => 'https://www.instagram.com/p/one/',
        'published_at' => now()->subHour(),
        'expires_at' => null,
        'like_count' => null,
        'comments_count' => null,
        'children' => [],
    ]];

    $sync->persistMedia($payload, false);
    $payload[0]['caption'] = 'Updated';
    $sync->persistMedia($payload, false);

    expect(InstagramMedia::query()->where('external_media_id', 'dup-1')->count())->toBe(1)
        ->and(InstagramMedia::query()->where('external_media_id', 'dup-1')->value('caption'))->toBe('Updated');
});

it('keeps the previous snapshot when the api rate-limits', function () {
    fakeInstagramConfig();
    InstagramMedia::factory()->create(['caption' => 'Cached post']);

    Http::fake([
        'graph.instagram.com/*' => Http::response(['error' => ['code' => 4, 'message' => 'rate limit']], 429),
    ]);

    $result = app(InstagramSyncService::class)->sync(feed: true, stories: false);

    expect($result['feed'])->toBeFalse()
        ->and($result['errors']['feed'] ?? null)->not->toBeEmpty()
        ->and(InstagramMedia::query()->where('caption', 'Cached post')->exists())->toBeTrue();
});

it('does not retry invalid tokens and records a safe error', function () {
    fakeInstagramConfig();

    Http::fake([
        'graph.instagram.com/*' => Http::response(['error' => ['code' => 190, 'message' => 'Invalid OAuth access token.']], 400),
    ]);

    $result = app(InstagramSyncService::class)->sync(feed: true, stories: true);

    expect($result['errors'])->not->toBeEmpty()
        ->and(InstagramSetting::current()->fresh()->token_status)->toBe('invalid')
        ->and(json_encode($result))->not->toContain('TEST_TOKEN_NOT_FOR_PRODUCTION');
});

it('blocks unauthenticated instagram cms access', function () {
    $this->get('/internal/instagram')->assertRedirect();
});

it('allows authorized users to view instagram cms and sync', function () {
    fakeInstagramConfig();
    Http::fake(function (Request $request) {
        $url = $request->url();
        if (str_contains($url, '/media')) {
            return Http::response(['data' => []]);
        }
        if (str_contains($url, '/stories')) {
            return Http::response(['data' => []]);
        }

        return Http::response([
            'id' => '17841400000000000',
            'username' => 'epikepc',
            'profile_picture_url' => 'https://scontent.cdninstagram.com/avatar.jpg',
        ]);
    });

    $user = instagramUser(['view_instagram', 'manage_instagram', 'sync_instagram']);

    $this->actingAs($user)->get('/internal/instagram')->assertOk()->assertDontSee('TEST_TOKEN_NOT_FOR_PRODUCTION');

    $this->actingAs($user)
        ->postJson('/internal/instagram/sync', ['scope' => 'all'])
        ->assertOk();
});

it('hides feed items that admins mark invisible', function () {
    fakeInstagramConfig();
    $item = InstagramMedia::factory()->create(['is_visible' => true, 'caption' => 'Visible then hidden']);
    $user = instagramUser(['view_instagram', 'manage_instagram']);

    $this->actingAs($user)
        ->patchJson('/internal/instagram/media/'.$item->id.'/visibility', ['is_visible' => false])
        ->assertOk();

    $widget = app(InstagramFeedService::class)->buildWidget();
    expect(collect($widget['feed'])->pluck('id'))->not->toContain($item->id);
});

it('shows cms highlights as story rings and hides invisible ones', function () {
    fakeInstagramConfig();
    InstagramSetting::current()->update(['enabled' => true]);

    InstagramHighlight::factory()->create(['title' => 'Projects', 'sort_order' => 0]);
    InstagramHighlight::factory()->create(['title' => 'Safety', 'sort_order' => 1]);
    InstagramHighlight::factory()->hidden()->create(['title' => 'Hidden']);

    $widget = app(InstagramFeedService::class)->buildWidget();

    expect($widget['has_highlights'])->toBeTrue()
        ->and($widget['rings'])->toHaveCount(2)
        ->and(collect($widget['rings'])->pluck('label')->all())->toBe(['Projects', 'Safety'])
        ->and(collect($widget['rings'])->pluck('opens')->unique()->all())->toBe(['external']);
});

it('prepends a Latest ring when live stories and highlights both exist', function () {
    fakeInstagramConfig();
    InstagramSetting::current()->update(['enabled' => true]);

    InstagramMedia::factory()->story()->create();
    InstagramHighlight::factory()->create(['title' => 'Projects']);

    $widget = app(InstagramFeedService::class)->buildWidget();

    expect($widget['rings'][0]['label'])->toBe('Latest')
        ->and($widget['rings'][0]['opens'])->toBe('viewer')
        ->and($widget['rings'][1]['label'])->toBe('Projects');
});

it('allows authorized users to manage highlights', function () {
    $user = instagramUser(['view_instagram', 'manage_instagram']);

    $this->actingAs($user)
        ->postJson('/internal/instagram/highlights', [
            'title' => 'Projects',
            'cover_url' => '/storage/seed/gallery/image66.png',
            'permalink' => 'https://www.instagram.com/stories/highlights/1234567890/',
            'is_visible' => true,
        ])
        ->assertOk();

    expect(InstagramHighlight::query()->where('title', 'Projects')->exists())->toBeTrue();
});

it('does not expose the access token on the public homepage', function () {
    fakeInstagramConfig();
    InstagramSetting::current()->update(['enabled' => true]);
    InstagramMedia::factory()->create(['caption' => 'Site progress']);

    $this->get('/')
        ->assertOk()
        ->assertSee('Follow', false)
        ->assertSee('Our Journey', false)
        ->assertDontSee('TEST_TOKEN_NOT_FOR_PRODUCTION', false)
        ->assertDontSee('INSTAGRAM_ACCESS_TOKEN', false);
});
