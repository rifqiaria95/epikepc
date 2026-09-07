<?php

namespace App\Jobs;

use App\Enums\InstagramPostStatus;
use App\Models\InstagramPost;
use App\Services\Instagram\InstagramPublishService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishInstagramPostJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 600;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(public int $postId) {}

    public function uniqueId(): string
    {
        return 'instagram-publish-'.$this->postId;
    }

    public function handle(InstagramPublishService $publisher): void
    {
        $post = InstagramPost::query()->find($this->postId);

        if (! $post) {
            return;
        }

        if ($post->status === InstagramPostStatus::Published) {
            return;
        }

        if (! in_array($post->status, [InstagramPostStatus::Scheduled, InstagramPostStatus::Failed], true)) {
            return;
        }

        $publisher->publish($post);
    }
}
