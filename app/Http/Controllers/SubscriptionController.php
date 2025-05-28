<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\ActivityLog;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Menampilkan halaman pilihan paket.
     */
    public function index()
    {
        $plans = Plan::orderBy('rank', 'asc')->get(); // Urutkan plan berdasarkan rank
        $user = Auth::user();

        // Ambil semua langganan aktif pengguna beserta data plan-nya
        $activeUserSubscriptions = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('plan') // Eager load data plan
            ->get();

        // Buat pemetaan detail paket aktif untuk kemudahan di view
        $activePlanDetails = $activeUserSubscriptions->mapWithKeys(function ($sub) {
            if ($sub->plan) { // Pastikan relasi plan ada
                return [$sub->plan_id => [
                    'name' => $sub->plan->name,
                    'slug' => $sub->plan->slug,
                    'rank' => $sub->plan->rank,
                    'ends_at' => $sub->ends_at
                ]];
            }
            return []; // Jika plan tidak ada, kembalikan array kosong
        })->filter(); // Hapus entri kosong jika ada

        // Variabel $currentPlan (single) tidak lagi cukup karena user bisa punya banyak plan aktif.
        // Kita akan menggunakan $activePlanDetails di view.
        // Untuk kompatibilitas minimal dengan view lama Anda jika masih merujuk $currentPlan,
        // kita bisa set $currentPlan ke plan dengan rank tertinggi atau null.
        $currentPlan = null;
        if ($activePlanDetails->isNotEmpty()) {
            $highestRankPlanId = $activeUserSubscriptions->sortByDesc(function ($sub) {
                return $sub->plan ? $sub->plan->rank : 0;
            })->first()->plan_id;
            $currentPlan = Plan::find($highestRankPlanId);
        }


        return view('subscriptions.index', compact('plans', 'currentPlan', 'activePlanDetails'));
    }

    /**
     * Memproses langganan ke paket tertentu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = Auth::user();
        $selectedPlan = Plan::findOrFail($request->plan_id);

        $activeSubscriptionForSelectedPlan = $user->subscriptions()
            ->where('plan_id', $selectedPlan->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->first();

        $message = '';
        $activityDescription = ''; // Variabel untuk deskripsi log

        if ($activeSubscriptionForSelectedPlan) {
            // KASUS A: Perpanjang
            // ... (logika perpanjangan Anda dari jawaban sebelumnya) ...
            if (!$selectedPlan->duration_days && !$activeSubscriptionForSelectedPlan->ends_at) {
                return redirect()->route('subscription.plans')->with('info', 'Anda sudah memiliki paket ' . $selectedPlan->name . ' yang aktif tanpa batas waktu.');
            }
            if (!$selectedPlan->duration_days && $activeSubscriptionForSelectedPlan->ends_at) {
                $activeSubscriptionForSelectedPlan->update(['ends_at' => null]);
                $message = 'Paket ' . $selectedPlan->name . ' Anda sekarang aktif tanpa batas waktu.';
                $activityDescription = 'Memperpanjang paket ' . $selectedPlan->name . ' menjadi tanpa batas waktu.';
            } elseif ($selectedPlan->duration_days) {
                $newEndsAt = null;
                if ($activeSubscriptionForSelectedPlan->ends_at) {
                    $baseDateForExtension = $activeSubscriptionForSelectedPlan->ends_at->isPast() ? Carbon::now() : $activeSubscriptionForSelectedPlan->ends_at;
                    $newEndsAt = $baseDateForExtension->copy()->addDays($selectedPlan->duration_days);
                } else {
                    $newEndsAt = Carbon::now()->addDays($selectedPlan->duration_days);
                }
                $activeSubscriptionForSelectedPlan->update(['ends_at' => $newEndsAt]);
                $message = 'Durasi paket ' . $selectedPlan->name . ' Anda telah diperpanjang hingga ' . $newEndsAt->format('d M Y, H:i') . '.';
                $activityDescription = 'Memperpanjang paket ' . $selectedPlan->name . ' hingga ' . $newEndsAt->format('d M Y, H:i') . '.';
            }
        } else {
            // KASUS B: Berlangganan baru
            // ... (logika berlangganan baru Anda dari jawaban sebelumnya) ...
            $startsAt = Carbon::now();
            $endsAt = null;
            if ($selectedPlan->duration_days) {
                $endsAt = $startsAt->copy()->addDays($selectedPlan->duration_days);
            }
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $selectedPlan->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
            ]);
            $message = 'Anda berhasil berlangganan paket ' . $selectedPlan->name . '.';
            $activityDescription = 'Berlangganan paket ' . $selectedPlan->name;
            if ($endsAt) {
                $message .= ' Aktif hingga ' . $endsAt->format('d M Y, H:i') . '.';
                $activityDescription .= ' hingga ' . $endsAt->format('d M Y, H:i') . '.';
            } else {
                $activityDescription .= ' (tanpa batas waktu).';
            }
        }

        // Pencatatan Log Aktivitas untuk Berlangganan
        if (!empty($activityDescription)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'subscription_change', // atau 'subscribed_to_plan', 'extended_plan'
                'description' => $activityDescription,
                'ip_address' => $request->ip(), // Menggunakan $request yang di-inject
                'user_agent' => $request->userAgent(), // Menggunakan $request yang di-inject
            ]);
        }

        return redirect()->route('subscription.plans')->with('success', $message);
    }
}
