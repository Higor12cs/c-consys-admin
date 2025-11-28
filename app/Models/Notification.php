<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'context',
        'is_read',
    ];

    protected $casts = [
        'context' => 'array',
        'is_read' => 'boolean',
    ];
}
