<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorDaily extends Model
{
    public $timestamps = false;

    protected $table = 'indicators_daily';

    protected $fillable = [
        'customer_id',
        'date',
        'company',
        'indicator',
        'target',
        'actual',
        'direction',
    ];

    protected $casts = [
        'date' => 'datetime',
        'target' => 'decimal:2',
        'actual' => 'decimal:2',
        'direction' => 'integer',
    ];
}
