<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController; // Tambahkan ini

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/dashboard', function () {
    $user = Auth::user();
    $activePlanDetails = collect();
    $currentPlan = null; // Atau plan default jika tidak ada langganan

    if ($user) {
        $activePlanDetails = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->get()
            ->mapWithKeys(function ($sub) {
                if ($sub->plan) {
                    return [$sub->plan_id => [
                        'name' => $sub->plan->name,
                        'slug' => $sub->plan->slug,
                        'rank' => $sub->plan->rank,
                        'ends_at' => $sub->ends_at
                    ]];
                }
                return [];
            })->filter();

        if ($activePlanDetails->isNotEmpty()) {
            $highestRankSubscription = $user->subscriptions()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->with('plan')
                ->get()
                ->sortByDesc(function ($sub) {
                    return $sub->plan ? $sub->plan->rank : 0;
                })->first();

            if ($highestRankSubscription && $highestRankSubscription->plan) {
                $currentPlan = $highestRankSubscription->plan;
            }
        }
    }
    // Jika tidak ada paket aktif, Anda mungkin ingin menampilkan info default
    // atau membiarkan $currentPlan null dan menanganinya di view.

    return view('dashboard', compact('user', 'currentPlan', 'activePlanDetails'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/subscription-plans', [SubscriptionController::class, 'index'])->name('subscription.plans');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes untuk Langganan & Pembayaran
    // Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscription.store'); // Nonaktifkan atau modifikasi jika perlu

    // RUTE BARU untuk halaman checkout
    Route::get('/subscription/checkout/{plan_slug?}', [SubscriptionController::class, 'showCheckoutPageBySlug'])->name('subscription.checkout'); // Ganti nama method jika mau, atau tetap showCheckoutPage

    // Rute baru untuk inisiasi pembayaran
    Route::post('/subscription/initiate-payment', [SubscriptionController::class, 'initiatePayment'])->name('subscription.initiatePayment');

    // Rute untuk halaman setelah pembayaran (return_url dari Tripay)
    Route::get('/payment/finish', [PaymentController::class, 'paymentFinish'])->name('payment.finish');
    Route::get('/payment/instructions/{merchantRef}', [PaymentController::class, 'paymentInstructions'])->name('payment.instructions');


    Route::get('/profile/activity-log', [ActivityLogController::class, 'index'])->name('profile.activity-log');


    // Fitur berdasarkan langganan (contoh)
    Route::get('/gold-feature', function () {
        return "Ini adalah fitur Gold!";
    })->middleware(['auth', 'subscribed']); // Anda mungkin perlu menyesuaikan middleware 'subscribed'

    Route::get('/platinum-or-diamond-feature', function () {
        return "Ini adalah fitur Platinum!";
    })->middleware(['auth', 'subscribed:platinum,diamond']);

    Route::get('/diamond-feature', function () {
        return "Ini adalah fitur untuk platinum atau diamond!";
    })->middleware(['auth', 'subscribed:diamond']);
});

// Callback dari Tripay (tidak memerlukan auth middleware, tapi perlu CSRF exception)
Route::post('/tripay/callback', [PaymentController::class, 'handleCallback'])->name('tripay.callback');


require __DIR__ . '/auth.php';
