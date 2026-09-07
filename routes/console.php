<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('instagram:sync --feed')
    ->everyThirtyMinutes()
    ->withoutOverlapping(30)
    ->onOneServer();

Schedule::command('instagram:sync --stories')
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->onOneServer();

Schedule::command('instagram:publish-due')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->onOneServer();
