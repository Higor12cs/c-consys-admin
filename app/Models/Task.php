<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'customer_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'completed_at',
        'finished_at',
        'executed_by',
        'supervised_by',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date:Y-m-d',
        'completed_at' => 'date:Y-m-d',
        'finished_at' => 'date:Y-m-d',
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervised_by');
    }
}
