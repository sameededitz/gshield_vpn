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

Route::get('/mailable', function () {
    $user = App\Models\User::find(2);

    return new App\Mail\VerifyEmailMail($user, 'https://example.com/verify-email?email=' . $user->email . '&hash=' . sha1($user->email));
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created';
});
Route::get('/migrate', function () {
    Artisan::call('migrate');
    return 'Database migrated';
});
Route::get('/seed', function () {
    Artisan::call('db:seed');
    return 'Database seeded';
});
Route::get('/migrate-refresh-seed', function () {
    Artisan::call('migrate --seed');
    return 'Database migrated and seeded';
});
Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return 'Optimized and cleared';
});
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
