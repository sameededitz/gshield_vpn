<?php

use App\Http\Controllers\VerifyController;
use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', Logout::class)->name('logout')->middleware('auth');

Route::get('/email/verify/{id}/{hash}', [VerifyController::class, 'verify'])->middleware(['signed'])->name('verification.verify');