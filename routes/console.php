<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('purchase:expire')
    ->daily()
    ->onFailure(function () {
        Log::error('Failed to run purchase:expire command');
    });