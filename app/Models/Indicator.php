<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    protected $fillable = [
        'code',
        'description',
        'is_percentage',
    ];

    protected $casts = [
        'is_percentage' => 'boolean',
    ];
}
