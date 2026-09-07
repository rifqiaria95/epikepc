<?php

use App\Enums\InstagramPostStatus;
use App\Jobs\PublishInstagramPostJob;
use App\Models\InstagramPost;
use App\Services\Instagram\InstagramPublishService;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

function fakePublishConfig(): void
{
    fakeInstagramConfig();
    config()->set('queue.default', 'sync');
    config()->set('filesystems.disks.public.root', storage_path('framework/testing/disks/public'));
    Storage::fake('public');
}

it('publishes a due image post through content publishing api', function () {
    fakePublishConfig();

    Http::fake(function (Request $request) {
        $url = $request->url();
        if ($request->method() === 'POST' && str_contains($url, '/media_publish')) {
            return Http::response(['id' => 'published-99']);
        }
        if ($request->method() === 'POST' && str_contains($url, '/media')) {
            return Http::response(['id' => 'container-1']);
        }
        if (str_contains($url, 'container-1')) {
            return Http::response(['id' => 'container-1', 'status_code' => 'FINISHED']);
        }

        return Http::response(['data' => [], 'id' => '17841400000000000', 'username' => 'epikepc']);
    });

    Storage::disk('public')->put('uploads/instagram/posts/test.jpg', 'fake-image');

    $post = InstagramPost::factory()->due()->create([
        'media_path' => 'uploads/instagram/posts/test.jpg',
        'media_url' => 'https://example.test/storage/uploads/instagram/posts/test.jpg',
        'caption' => 'Go live',
    ]);

    app(InstagramPublishService::class)->publish($post);

    $post->refresh();
    expect($post->status)->toBe(InstagramPostStatus::Published)
        ->and($post->external_media_id)->toBe('published-99')
        ->and($post->container_id)->toBe('container-1');
});

it('schedules an instagram image post from cms', function () {
    fakePublishConfig();
    $user = instagramUser(['view_instagram', 'manage_instagram', 'publish_instagram']);

    $file = UploadedFile::fake()->create('site.jpg', 200, 'image/jpeg');

    $this->actingAs($user)
        ->postJson('/internal/instagram/posts', [
            'media_type' => 'IMAGE',
            'caption' => 'Scheduled from CMS #EPIKEPC',
            'scheduled_at' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'publish_now' => 0,
            'media' => $file,
        ])
        ->assertOk();

    $post = InstagramPost::query()->first();
    expect($post)->not->toBeNull()
        ->and($post->status)->toBe(InstagramPostStatus::Scheduled)
        ->and($post->caption)->toContain('Scheduled from CMS')
        ->and($post->media_path)->not->toBeEmpty();
});

it('dispatches due posts via artisan command', function () {
    fakePublishConfig();
    Queue::fake();

    $due = InstagramPost::factory()->due()->create();
    InstagramPost::factory()->create([
        'status' => InstagramPostStatus::Scheduled,
        'scheduled_at' => now()->addDay(),
    ]);

    $this->artisan('instagram:publish-due')->assertSuccessful();

    Queue::assertPushed(PublishInstagramPostJob::class, fn ($job) => $job->postId === $due->id);
});

it('cancels a scheduled post', function () {
    fakePublishConfig();
    $user = instagramUser(['publish_instagram', 'manage_instagram']);
    $post = InstagramPost::factory()->create();

    $this->actingAs($user)
        ->postJson('/internal/instagram/posts/'.$post->id.'/cancel')
        ->assertOk();

    expect($post->fresh()->status)->toBe(InstagramPostStatus::Cancelled);
});
