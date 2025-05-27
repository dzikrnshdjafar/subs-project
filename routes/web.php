<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes untuk Langganan
    Route::get('/subscription-plans', [SubscriptionController::class, 'index'])->name('subscription.plans');
    Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscription.store');

    // // Hanya bisa diakses jika punya langganan aktif (apapun paketnya)
    // Route::get('/premium-feature', function () {
    //     return "Ini adalah fitur premium/basic!";
    // })->middleware(['auth', 'subscribed']);

    // // Hanya bisa diakses oleh pengguna dengan paket 'premium'
    // Route::get('/super-premium-feature', function () {
    //     return "Ini adalah fitur SUPER premium!";
    // })->middleware(['auth', 'subscribed:premium']);

    // // Bisa diakses oleh pengguna 'basic' atau 'premium'
    // Route::get('/basic-or-premium-feature', function () {
    //     return "Ini adalah fitur untuk Basic atau Premium!";
    // })->middleware(['auth', 'subscribed:basic,premium']);
});




require __DIR__ . '/auth.php';
