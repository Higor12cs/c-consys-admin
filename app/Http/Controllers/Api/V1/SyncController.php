<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncDailyRequest;
use App\Http\Requests\SyncMonthlyRequest;
use App\Jobs\ProcessDailySync;
use App\Jobs\ProcessMonthlySync;
use App\Models\SyncLog;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function daily(SyncDailyRequest $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        $records = $request->input('records');

        $syncLog = SyncLog::create([
            'customer_id' => $customerId,
            'type' => 'daily',
            'sync_date' => now(),
            'status' => 'processing',
        ]);

        ProcessDailySync::dispatch($customerId, $records, $syncLog->id);

        return response()->json([
            'success' => true,
            'message' => count($records).' records received and queued for processing.',
            'sync_id' => $syncLog->id,
        ]);
    }

    public function monthly(SyncMonthlyRequest $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        $records = $request->input('records');

        $syncLog = SyncLog::create([
            'customer_id' => $customerId,
            'type' => 'monthly',
            'sync_date' => now(),
            'status' => 'processing',
        ]);

        ProcessMonthlySync::dispatch($customerId, $records, $syncLog->id);

        return response()->json([
            'success' => true,
            'message' => count($records).' records received and queued for processing.',
            'sync_id' => $syncLog->id,
        ]);
    }
}
