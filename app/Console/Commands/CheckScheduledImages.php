<?php

namespace App\Console\Commands;

use App\Jobs\GenerateImageJob;
use App\Models\Schedule;
use App\Models\ScheduleExecutionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class CheckScheduledImages extends Command
{
    protected $signature = 'images:check-schedules';

    protected $description = 'Check and execute scheduled images';

    public function handle()
    {
        $now = now();
        $currentTime = $now->format('H:i');
        $currentDate = now()->toDateTimeString();
        $dayOfWeek = strtolower($now->format('D'));

        $dayColumn = match ($dayOfWeek) {
            'sun' => 'sun',
            'mon' => 'mon',
            'tue' => 'tue',
            'wed' => 'wed',
            'thu' => 'thu',
            'fri' => 'fri',
            'sat' => 'sat',
        };

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $timeExpression = "to_char(time, 'HH24:MI')";
        } elseif ($driver === 'sqlite') {
            $timeExpression = "strftime('%H:%M', time)";
        } else {
            return;
        }

        $schedules = Schedule::where('is_active', true)
            ->whereRaw("{$timeExpression} <= ?", [$currentTime])
            ->get();

        \Log::info('Found '.$schedules->count().' active schedules to check for execution.');

        foreach ($schedules as $schedule) {
            $images = $schedule->images()
                ->where('is_active', true)
                ->wherePivot($dayColumn, true)
                ->get();

            \Log::info('Found '.$images->count()." active images for schedule ID: {$schedule->id} on day: {$dayColumn}");

            foreach ($images as $image) {
                $alreadyExecuted = ScheduleExecutionLog::where('image_id', $image->id)
                    ->where('schedule_id', $schedule->id)
                    ->where('execution_date', '<=', $currentDate)
                    ->where('status', 'success')
                    ->exists();

                \Log::info("ScheduleExecutionLog check for image ID: {$image->id}, schedule ID: {$schedule->id}, date: {$currentDate}, already executed: ".($alreadyExecuted ? 'yes' : 'no'));

                if ($alreadyExecuted) {
                    continue;
                }

                \Log::info("Dispatching GenerateImageJob for image ID: {$image->id}, schedule ID: {$schedule->id}");

                $destinations = collect($image->destinations)->map(function ($destination, $index) {
                    return [
                        'value' => $destination['value'],
                        'delay' => $index * 5,
                    ];
                })->toArray();

                \Log::info('Dispatching GenerateImageJob with destinations: '.json_encode($destinations));

                GenerateImageJob::dispatch($image, $schedule->id, $destinations);

                $this->info("Scheduled image generation: {$image->name} (ID: {$image->id})");
            }
        }

        return 0;
    }
}
