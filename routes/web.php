<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('login');
    }
})->name('home');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

Route::get('artisan/{command}', function ($command) {
    if (Auth::check() && Auth::user()->isAdmin()) {
        Artisan::call($command);
        return response()->json(['output' => Artisan::output(), 'status' => Artisan::output() ? 'success' : 'error', 'command' => $command]);
    }
    return response()->json(['error' => 'Unauthorized'], 403);
})->where('command', '.*');

// make me login api fn i can login via in it just by typing email like login as this user and give me a token
Route::get('/login-as/{email}', function ($email) {
    $user = App\Models\User::where('email', $email)->first();
    if ($user) {
        Auth::login($user);
        $token = $user->createToken('LoginAsToken')->plainTextToken;
        return response()->json([
            'message' => 'Logged in as ' . $user->email,
            'token' => $token,
        ]);
    } else {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }
})->name('login-as')->middleware('guest');
