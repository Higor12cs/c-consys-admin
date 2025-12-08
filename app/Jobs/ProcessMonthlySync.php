<?php

namespace App\Jobs;

use App\Models\IndicatorMonthly;
use App\Models\SyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMonthlySync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $customerId,
        private array $records,
        private int $syncLogId
    ) {}

    public function handle(): void
    {
        $syncLog = SyncLog::find($this->syncLogId);

        try {
            DB::transaction(function () use ($syncLog) {
                IndicatorMonthly::where('customer_id', $this->customerId)->delete();

                $inserted = 0;
                foreach ($this->records as $record) {
                    IndicatorMonthly::create([
                        'customer_id' => $this->customerId,
                        'date' => $record['date'],
                        'company' => $record['company'],
                        'year' => $record['year'],
                        'month' => $record['month'],
                        'indicator' => $record['indicator'],
                        'description' => $record['description'] ?? null,
                        'target' => $record['target'],
                        'actual' => $record['actual'],
                    ]);
                    $inserted++;
                }

                $lastDate = collect($this->records)->max('date');

                $syncLog->update([
                    'status' => 'success',
                    'inserted' => $inserted,
                    'last_data_date' => $lastDate,
                ]);
            });
        } catch (\Exception $e) {
            $syncLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Monthly sync failed', [
                'customer_id' => $this->customerId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
