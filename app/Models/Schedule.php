<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model
{
    protected $fillable = [
        'time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'image_schedule')
            ->withPivot(['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'])
            ->withTimestamps();
    }
}
