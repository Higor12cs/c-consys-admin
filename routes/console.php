<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('view:clear', function () {
    $views = glob(storage_path('framework/views/*.php'));

    foreach ($views as $view) {
        @unlink($view);
    }

    $this->info('Compiled views cleared!');
})->everyMinute();

Schedule::command('images:check-schedules')->everyMinute();
