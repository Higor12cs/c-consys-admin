<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('view:clear', function () {
    $views = glob(storage_path('framework/views/*.php'));

    foreach ($views as $view) {
        @unlink($view);
    }

    $this->info('Compiled views cleared!');
})->everyThirtyMinutes();

Schedule::command('images:check-schedules')->everyThirtyMinutes();
