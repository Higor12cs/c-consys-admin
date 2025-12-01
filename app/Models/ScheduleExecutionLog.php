<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleExecutionLog extends Model
{
    use Prunable;

    protected $fillable = [
        'image_id',
        'schedule_id',
        'execution_date',
        'status',
        'error_message',
    ];

    protected $casts = [
        'execution_date' => 'timestamp:Y-m-d H:i:s',
    ];

    public function prunable()
    {
        return static::where('execution_date', '<', Carbon::now()->subDays(90));
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
