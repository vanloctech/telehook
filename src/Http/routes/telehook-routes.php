<?php
use Illuminate\Support\Facades\Route;
use Vanloctech\Telehook\Http\Controllers\TelehookController;

Route::post(config('telehook.token', '') . '/' . config('telehook.path', 'webhook'),
    TelehookController::class)
    ->name('telegram-webhook');
