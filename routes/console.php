<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('purchases:expire-ended')
    ->daily()
    ->onFailure(function () {
        Log::error('Failed to run purchases:expire-ended command');
    });

Schedule::command('qr:clean-expired')
    ->everyFifteenMinutes()
    ->onFailure(function () {
        Log::error('Failed to run qr:clean-expired command');
    });
