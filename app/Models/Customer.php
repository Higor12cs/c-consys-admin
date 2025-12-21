<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'api_token',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
