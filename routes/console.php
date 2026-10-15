<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Purge expired Sanctum integration tokens daily.
Schedule::command('sanctum:purge --expired=24')->daily();

// Nightly database + uploaded-files backup (kept 14 days by default).
Schedule::command('aurevia:backup')->dailyAt('02:30')->withoutOverlapping();
