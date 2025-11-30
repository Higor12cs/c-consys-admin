<?php

namespace App\Console\Commands;

use App\Jobs\GenerateImageJob;
use App\Models\Schedule;
use App\Models\ScheduleExecutionLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckScheduledImages extends Command
{
    protected $signature = 'images:check-schedules';

    protected $description = 'Check and execute scheduled images';

    public function handle()
    {
        $currentTime = now()->format('H:i');
        $currentDate = now()->toDateTimeString();
        $dayOfWeek = strtolower(now()->format('D'));

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

        \Log::info('Found '.$schedules->count()." active schedules to process at time {$currentTime}.");

        foreach ($schedules as $schedule) {
            $images = $schedule->images()
                ->with('customer')
                ->where('is_active', true)
                ->wherePivot($dayColumn, true)
                ->get();

            foreach ($images as $image) {
                $alreadyExecuted = ScheduleExecutionLog::where('image_id', $image->id)
                    ->where('schedule_id', $schedule->id)
                    ->where('execution_date', '<=', $currentDate)
                    ->where('status', 'success')
                    ->exists();

                \Log::info("Checking Image ID: {$image->id} | Customer: {$image->customer->name} | Under Schedule ID: {$schedule->id} | Already Executed: ".($alreadyExecuted ? 'Yes' : 'No'));

                if ($alreadyExecuted) {
                    continue;
                }

                \Log::info("Dispatching job for Image ID: {$image->id} | Customer: {$image->customer->name} | Under Schedule ID: {$schedule->id}");

                $destinations = collect($image->destinations)->map(function ($destination, $index) {
                    return [
                        'value' => $destination['value'],
                        'delay' => $index * 5,
                    ];
                })->toArray();

                GenerateImageJob::dispatch($image, $schedule->id, $destinations);
            }
        }

        return 0;
    }
}
