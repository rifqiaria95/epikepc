<?php

namespace App\Jobs;

use App\Services\Instagram\InstagramSyncService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncInstagramJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 120;

    public int $tries = 1;

    public function __construct(
        public bool $feed = true,
        public bool $stories = true,
    ) {}

    public function uniqueId(): string
    {
        return 'instagram-sync';
    }

    public function handle(InstagramSyncService $sync): void
    {
        $sync->sync(feed: $this->feed, stories: $this->stories);
    }
}
