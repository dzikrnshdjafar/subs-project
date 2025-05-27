<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // Menampilkan halaman pilihan paket
    public function index()
    {
        $plans = Plan::all();
        $currentPlan = Auth::user()->getCurrentPlan();
        return view('subscriptions.index', compact('plans', 'currentPlan'));
    }

    // Memproses langganan ke paket tertentu
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        // Nonaktifkan langganan lama jika ada (kecuali jika upgrade dari free)
        $user->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

        $startsAt = Carbon::now();
        $endsAt = null;

        if ($plan->duration_days) {
            $endsAt = $startsAt->copy()->addDays($plan->duration_days);
        }

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
        ]);

        // Di sini idealnya ada integrasi payment gateway jika plan berbayar
        // Untuk contoh ini, kita langsung aktifkan

        return redirect()->route('dashboard')->with('success', 'Anda berhasil berlangganan paket ' . $plan->name . '!');
    }
}
