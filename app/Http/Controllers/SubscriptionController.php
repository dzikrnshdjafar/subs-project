<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User; // Tambahkan ini
use App\Models\Payment; // Tambahkan ini
use App\Models\ActivityLog;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\TripayService; // Tambahkan ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;   // Tambahkan ini
use Illuminate\Support\Facades\DB;    // Tambahkan ini

class SubscriptionController extends Controller
{
    protected TripayService $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    public function index()
    {
        // ... (method index yang sudah ada, tidak perlu diubah untuk bagian ini)
        // $paymentChannels dihilangkan dari sini karena akan diambil di halaman checkout
        $plans = Plan::orderBy('rank', 'asc')->get();
        $user = Auth::user();
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

        $currentPlan = null;
        if ($activePlanDetails->isNotEmpty()) {
            $highestRankPlanId = $user->subscriptions()->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->with('plan')
                ->get()
                ->sortByDesc(function ($sub) {
                    return $sub->plan ? $sub->plan->rank : 0;
                })->first()->plan_id ?? null; // Tambah null coalescing operator
            if ($highestRankPlanId) {
                $currentPlan = Plan::find($highestRankPlanId);
            }
        }
        // $paymentChannels = $this->tripayService->getPaymentChannels(); // Pindahkan ini ke showCheckoutPage

        return view('subscriptions.index', compact('plans', 'currentPlan', 'activePlanDetails'));
    }

    public function showCheckoutPageBySlug(Request $request, $plan_slug = null) // $plan_slug sekarang opsional
    {
        $user = Auth::user();
        $allAvailablePlans = Plan::orderBy('rank', 'asc')->get();

        if ($allAvailablePlans->isEmpty()) {
            // Tidak ada plan sama sekali, redirect atau tampilkan error
            return redirect()->route('subscription.plans')->with('error', 'Tidak ada paket langganan yang tersedia saat ini.');
        }

        $selectedPlan = null;
        $invalidSlugRedirectMessage = null;

        if ($plan_slug) {
            $selectedPlan = $allAvailablePlans->firstWhere('slug', $plan_slug);
            if (!$selectedPlan) {
                // Slug diberikan tapi tidak valid, siapkan pesan dan set selectedPlan ke default
                $invalidSlugRedirectMessage = 'Paket langganan dengan slug "' . e($plan_slug) . '" tidak ditemukan. Silakan pilih dari daftar.';
                // Log::warning("Invalid plan slug '{$plan_slug}' provided for checkout. Defaulting to first available plan.");
            }
        }

        // Jika tidak ada slug, atau slug tidak valid, pilih plan pertama sebagai default
        if (!$selectedPlan) {
            $selectedPlan = $allAvailablePlans->first();
        }

        // Jika $selectedPlan masih null (seharusnya tidak terjadi jika $allAvailablePlans tidak kosong)
        if (!$selectedPlan) {
            return redirect()->route('subscription.plans')->with('error', 'Gagal memuat detail paket. Silakan coba lagi.');
        }


        $paymentChannels = $this->tripayService->getPaymentChannels();
        if (empty($paymentChannels)) {
            Log::warning('Tripay payment channels are empty for checkout page.', ['user_id' => $user->id, 'selected_plan_id' => $selectedPlan->id]);
            return redirect()->route('subscription.plans')->with('error', 'Metode pembayaran tidak tersedia saat ini.');
        }

        // Cek apakah plan default ini sudah dimiliki user dan lifetime
        $isThisPlanCurrentlyActiveNonExpiring = $user->subscriptions()
            ->where('plan_id', $selectedPlan->id)
            ->where('status', 'active')
            ->whereNull('ends_at')
            ->exists();

        if ($isThisPlanCurrentlyActiveNonExpiring && !$selectedPlan->duration_days) {
            // Jika plan default sudah dimiliki dan lifetime, mungkin lebih baik redirect ke dashboard atau halaman plan
            // Namun, jika ada invalidSlugRedirectMessage, tampilkan itu dulu agar user tahu kenapa dia tidak di plan yg diminta
            if (!$invalidSlugRedirectMessage) {
                return redirect()->route('subscription.plans')->with('info', 'Anda sudah memiliki paket ' . $selectedPlan->name . ' yang aktif tanpa batas waktu.');
            }
        }

        $viewData = [
            'selectedPlan' => $selectedPlan,
            'allAvailablePlans' => $allAvailablePlans,
            'paymentChannels' => $paymentChannels,
            'user' => $user
        ];

        // Jika ada pesan karena slug tidak valid, tambahkan ke session flash
        if ($invalidSlugRedirectMessage) {
            return redirect()->route('subscription.checkout', ['plan_slug' => $selectedPlan->slug]) // Redirect ke URL yg benar dengan slug default
                ->with('warning', $invalidSlugRedirectMessage) // Gunakan warning atau info
                ->withInput($viewData); // Ini mungkin tidak terlalu berguna karena kita redirect, tapi untuk jaga-jaga
        }


        return view('subscriptions.checkout', $viewData);
    }



    public function initiatePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);
        $paymentMethod = $request->payment_method;

        // Cek apakah ada transaksi UNPAID untuk plan yang sama oleh user yang sama
        $existingUnpaidPayment = Payment::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', 'UNPAID')
            ->where('expired_time', '>', now())
            ->first();

        if ($existingUnpaidPayment && $existingUnpaidPayment->checkout_url) {
            // Jika ada dan masih valid, redirect ke checkout_url yang sudah ada
            return redirect()->away($existingUnpaidPayment->checkout_url);
        }


        DB::beginTransaction();
        try {
            $tripayData = $this->tripayService->createTransaction($user, $plan, $paymentMethod);

            if (empty($tripayData) || !isset($tripayData['reference'])) {
                Log::error('Tripay response data is empty or missing reference.', ['tripay_data' => $tripayData]);
                DB::rollBack();
                return redirect()->route('subscription.plans')->with('error', 'Gagal memproses permintaan pembayaran ke Tripay. Data tidak lengkap.');
            }

            // Simpan detail transaksi ke database kita
            $payment = Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'merchant_ref' => $tripayData['merchant_ref'],
                'tripay_reference' => $tripayData['reference'],
                'payment_method_code' => $tripayData['payment_method'],
                'payment_method_name' => $tripayData['payment_name'],
                'amount' => $tripayData['amount'],
                'fee_merchant' => $tripayData['fee_merchant'] ?? 0,
                'fee_customer' => $tripayData['fee_customer'] ?? 0,
                'total_fee' => $tripayData['total_fee'] ?? 0,
                'amount_received' => $tripayData['amount_received'] ?? 0,
                'pay_code' => $tripayData['pay_code'] ?? null,
                'checkout_url' => $tripayData['checkout_url'] ?? null,
                'status' => $tripayData['status'], // Seharusnya UNPAID
                'expired_time' => isset($tripayData['expired_time']) ? Carbon::createFromTimestamp($tripayData['expired_time']) : null,
                'instructions' => $tripayData['instructions'] ?? null,
            ]);

            DB::commit();

            // Redirect ke halaman pembayaran Tripay atau tampilkan instruksi
            if (!empty($tripayData['checkout_url'])) {
                return redirect()->away($tripayData['checkout_url']);
            } elseif (!empty($tripayData['pay_code'])) {
                // Jika tidak ada checkout_url (misal transfer bank manual), tampilkan instruksi
                return redirect()->route('payment.instructions', $payment->merchant_ref)
                    ->with('success', 'Instruksi pembayaran telah dibuat.');
            } else {
                Log::error('Tripay tidak memberikan checkout_url maupun pay_code.', ['tripay_data' => $tripayData]);
                return redirect()->route('subscription.plans')->with('error', 'Gagal mendapatkan detail pembayaran dari Tripay.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error initiating payment: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('subscription.plans')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
