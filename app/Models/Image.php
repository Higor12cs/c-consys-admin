<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Image extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'company',
        'indicators',
        'charts',
        'destinations',
        'is_active',
    ];

    protected $casts = [
        'indicators' => 'array',
        'charts' => 'array',
        'destinations' => 'array',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'image_schedule')
            ->withPivot(['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'])
            ->withTimestamps();
    }
}
