<?php

namespace App\Console\Commands;

use App\Jobs\PublishInstagramPostJob;
use App\Models\InstagramPost;
use Illuminate\Console\Command;

class PublishDueInstagramPostsCommand extends Command
{
    protected $signature = 'instagram:publish-due {--limit=10 : Max posts to dispatch per run}';

    protected $description = 'Dispatch due scheduled Instagram posts for Content Publishing';

    public function handle(): int
    {
        if (! config('instagram.enabled')) {
            $this->warn('Instagram integration is disabled.');

            return self::SUCCESS;
        }

        $limit = max(1, min(50, (int) $this->option('limit')));

        $ids = InstagramPost::query()
            ->due()
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info('No due Instagram posts.');

            return self::SUCCESS;
        }

        foreach ($ids as $id) {
            PublishInstagramPostJob::dispatch((int) $id);
            $this->line('Dispatched post #'.$id);
        }

        $this->info('Dispatched '.$ids->count().' Instagram post(s).');

        return self::SUCCESS;
    }
}
