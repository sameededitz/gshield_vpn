<?php

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', Logout::class)->name('logout')->middleware('auth');