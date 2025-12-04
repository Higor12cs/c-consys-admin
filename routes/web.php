<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TestImageController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => inertia('Auth/Login'))->middleware('guest')->name('login');
Route::post('/login', LoginController::class)->middleware('guest')->name('login.attempt');
Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/regenerate-token', [CustomerController::class, 'regenerate'])->name('customers.regenerate-token');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/finish', [TaskController::class, 'finish'])->name('tasks.finish');

    Route::prefix('/indicators')->as('indicators.')->group(function () {
        Route::get('/', fn () => inertia('Indicators/Index'))->name('index');

        Route::get('/indicators', [IndicatorController::class, 'index'])->name('indicators.index');
        Route::get('/indicators/create', [IndicatorController::class, 'create'])->name('indicators.create');
        Route::post('/indicators', [IndicatorController::class, 'store'])->name('indicators.store');
        Route::get('/indicators/{indicator}/edit', [IndicatorController::class, 'edit'])->name('indicators.edit');
        Route::put('/indicators/{indicator}', [IndicatorController::class, 'update'])->name('indicators.update');
        Route::delete('/indicators/{indicator}', [IndicatorController::class, 'destroy'])->name('indicators.destroy');

        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::post('/schedules/{schedule}/resend', [ScheduleController::class, 'resend'])->name('schedules.resend');

        Route::get('/images', [ImageController::class, 'index'])->name('images.index');
        Route::get('/images/create', [ImageController::class, 'create'])->name('images.create');
        Route::post('/images', [ImageController::class, 'store'])->name('images.store');
        Route::get('/images/{image}/edit', [ImageController::class, 'edit'])->name('images.edit');
        Route::put('/images/{image}', [ImageController::class, 'update'])->name('images.update');
        Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');

        Route::get('/images/{image}/preview', [TestImageController::class, 'preview'])->name('images.preview');
        Route::get('/images/{image}/send', [TestImageController::class, 'send'])->name('images.send');
    });

    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/whatsapp/groups', [WhatsAppController::class, 'index'])->name('whatsapp.groups');
    Route::post('/whatsapp/groups', [WhatsAppController::class, 'store'])->name('whatsapp.groups.create');
});

Route::redirect('/', '/login');
