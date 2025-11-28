<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'sync_date',
        'last_data_date',
        'inserted',
        'updated',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sync_date' => 'datetime',
        'last_data_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted(): void
    {
        static::created(function () {
            self::cleanOldLogs();
        });
    }

    private static function cleanOldLogs(): void
    {
        $daysToKeep = 90;
        self::where('created_at', '<', now()->subDays($daysToKeep))->delete();
    }
}
