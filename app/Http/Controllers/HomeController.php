<?php

namespace App\Http\Controllers;

use App\Models\Plan; // Pastikan model Plan di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan Auth di-import
use Illuminate\View\View; // Import View

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     */
    public function index(): View
    {
        $plans = Plan::orderBy('rank', 'asc')->get();
        $user = Auth::user();
        $activePlanDetails = collect(); // Inisialisasi sebagai collection kosong
        $currentPlan = null; // Inisialisasi

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
                        return [$sub->plan_id => [ // Menggunakan plan_id sebagai key
                            'name' => $sub->plan->name,
                            'slug' => $sub->plan->slug,
                            'rank' => $sub->plan->rank,
                            'ends_at' => $sub->ends_at
                        ]];
                    }
                    return [];
                })->filter();

            if ($activePlanDetails->isNotEmpty()) {
                // Logika untuk mendapatkan plan aktif dengan rank tertinggi
                $highestRankSubscription = $user->subscriptions()
                    ->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                    })
                    ->with('plan') // Eager load plan
                    ->get()
                    ->sortByDesc(function ($sub) {
                        return $sub->plan ? $sub->plan->rank : 0;
                    })->first();

                if ($highestRankSubscription && $highestRankSubscription->plan) {
                    $currentPlan = $highestRankSubscription->plan;
                }
            }
        }

        return view('home', compact('plans', 'currentPlan', 'activePlanDetails'));
    }
}
