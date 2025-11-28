<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorMonthly extends Model
{
    public $timestamps = false;

    protected $table = 'indicators_monthly';

    protected $fillable = [
        'customer_id',
        'date',
        'company',
        'year',
        'month',
        'indicator',
        'target',
        'actual',
    ];

    protected $casts = [
        'date' => 'datetime',
        'year' => 'integer',
        'month' => 'integer',
        'target' => 'decimal:2',
        'actual' => 'decimal:2',
    ];
}
