<?php
use Illuminate\Support\Facades\Route;
use Vanloctech\Telehook\Http\Controllers\TelehookController;
use Vanloctech\Telehook\TelehookSupport;

Route::post(TelehookSupport::getConfig('token', '') . '/' . TelehookSupport::getConfig('path', 'webhook'),
    TelehookController::class)
    ->name('telegram-webhook');
