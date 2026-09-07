<?php

namespace App\Console\Commands;

use App\Services\Instagram\InstagramSyncService;
use Illuminate\Console\Command;

class SyncInstagramCommand extends Command
{
    protected $signature = 'instagram:sync
                            {--feed : Sync the Instagram feed only}
                            {--stories : Sync active Instagram stories only}';

    protected $description = 'Synchronize Instagram feed and active stories into the local snapshot.';

    public function handle(InstagramSyncService $sync): int
    {
        $feedOnly = (bool) $this->option('feed');
        $storiesOnly = (bool) $this->option('stories');
        $feed = $feedOnly || ! $storiesOnly;
        $stories = $storiesOnly || ! $feedOnly;

        if (! config('instagram.enabled')) {
            $this->warn('Instagram integration is disabled.');

            return self::SUCCESS;
        }

        $result = $sync->sync(feed: $feed, stories: $stories);

        foreach (['profile', 'feed', 'stories'] as $scope) {
            if (! empty($result[$scope])) {
                $this->info(ucfirst($scope).' sync completed.');
            }
        }

        foreach ($result['errors'] as $scope => $message) {
            $this->error(ucfirst($scope).': '.$message);
        }

        return $result['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
