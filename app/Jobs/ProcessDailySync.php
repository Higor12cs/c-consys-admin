<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\IndicatorDaily;
use App\Models\SyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDailySync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $customerId,
        private array $records,
        private int $syncLogId
    ) {
    }

    public function handle(): void
    {
        $syncLog = SyncLog::find($this->syncLogId);

        try {
            DB::transaction(function () use ($syncLog) {
                IndicatorDaily::where('customer_id', $this->customerId)->delete();

                $inserted = 0;
                foreach ($this->records as $record) {
                    IndicatorDaily::create([
                        'customer_id' => $this->customerId,
                        'date' => $record['date'],
                        'company' => $record['company'],
                        'indicator' => $record['indicator'],
                        'target' => $record['target'],
                        'actual' => $record['actual'],
                        'direction' => $record['direction'],
                    ]);
                    $inserted++;
                }

                $lastDate = collect($this->records)->max('date');

                Customer::where('id', $this->customerId)->update([
                    'last_synced_at' => $lastDate,
                ]);

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

            Log::error('Daily sync failed', [
                'customer_id' => $this->customerId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
