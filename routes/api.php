<?php

use App\Http\Controllers\Api\V1\SyncController;
use App\Http\Middleware\SyncMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->middleware(SyncMiddleware::class)->group(function () {
    Route::post('/sync/daily', [SyncController::class, 'daily']);
    Route::post('/sync/monthly', [SyncController::class, 'monthly']);
});
