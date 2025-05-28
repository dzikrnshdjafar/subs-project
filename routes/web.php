<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
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

    // Hanya bisa diakses jika punya langganan aktif (apapun paketnya)
    Route::get('/gold-feature', function () {
        return "Ini adalah fitur Gold!";
    })->middleware(['auth', 'subscribed']);

    // Hanya bisa diakses oleh pengguna dengan paket 'diamond'
    Route::get('/platinum-or-diamond-feature', function () {
        return "Ini adalah fitur Platinum!";
    })->middleware(['auth', 'subscribed:platinum,diamond']);

    // Bisa diakses oleh pengguna 'platinum' atau 'diamond'
    Route::get('/diamond-feature', function () {
        return "Ini adalah fitur untuk platinum atau diamond!";
    })->middleware(['auth', 'subscribed:diamond']);

    Route::get('/profile/activity-log', [ActivityLogController::class, 'index'])->name('profile.activity-log');
});




require __DIR__ . '/auth.php';
