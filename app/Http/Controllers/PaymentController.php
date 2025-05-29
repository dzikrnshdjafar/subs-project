<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\TripayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Jika perlu untuk halaman finish

class PaymentController extends Controller
{
    protected TripayService $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    public function handleCallback(Request $request)
    {
        Log::info('Tripay Callback Received:', $request->all());

        $callbackSignature = $request->header('X-Callback-Signature');
        $json = $request->getContent(); // Ambil raw JSON body

        if (!$this->tripayService->validateCallbackSignature(['json_data' => $json, 'signature' => $callbackSignature])) {
            Log::warning('Tripay Callback: Invalid signature.');
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 400);
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Tripay Callback: Invalid JSON data.');
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON data',
            ], 400);
        }


        $payment = Payment::where('merchant_ref', $data['merchant_ref'])->first();

        if (!$payment) {
            Log::warning('Tripay Callback: Payment not found for merchant_ref: ' . $data['merchant_ref']);
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        // Hindari double processing jika status sudah PAID atau FAILED
        if (in_array($payment->status, ['PAID', 'FAILED', 'EXPIRED'])) {
            Log::info('Tripay Callback: Payment already processed or final.', ['merchant_ref' => $data['merchant_ref'], 'status' => $payment->status]);
            return response()->json(['success' => true, 'message' => 'Payment already processed or final.']);
        }

        DB::beginTransaction();
        try {
            // Update status payment lokal
            $payment->tripay_reference = $data['reference']; // Simpan referensi Tripay jika belum ada
            $payment->status = strtoupper($data['status']);
            $payment->amount_received = $data['amount_received'] ?? $payment->amount_received; // Ambil dari callback jika ada
            $payment->pay_code = $data['pay_code'] ?? $payment->pay_code;
            $payment->payment_method_code = $data['payment_method'] ?? $payment->payment_method_code;
            $payment->payment_method_name = $data['payment_name'] ?? $payment->payment_method_name; // Ambil payment_name jika ada
            $payment->note = $data['note'] ?? $payment->note;

            if (isset($data['expired_time'])) {
                $payment->expired_time = Carbon::createFromTimestamp($data['expired_time']);
            }
            if (isset($data['instructions'])) {
                $payment->instructions = $data['instructions'];
            }
            $payment->save();

            if ($payment->status === 'PAID') {
                $user = $payment->user;
                $plan = $payment->plan;

                // Cek apakah sudah ada subscription_id di payment, jika iya berarti ini perpanjangan
                // atau aktivasi yang sudah pernah dibuat recordnya (meski mungkin belum aktif).
                $existingSubscription = $payment->subscription_id ? Subscription::find($payment->subscription_id) : null;

                // Cari langganan aktif untuk plan yang sama
                if (!$existingSubscription) {
                    $existingSubscription = $user->subscriptions()
                        ->where('plan_id', $plan->id)
                        ->where('status', 'active') // Bisa juga cari yang 'pending_payment' jika ada logika itu
                        ->where(function ($q) {
                            $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                        })
                        ->first();
                }


                $message = '';
                $activityDescription = '';

                if ($existingSubscription && $existingSubscription->plan_id == $plan->id) {
                    // KASUS A: Perpanjang
                    if (!$plan->duration_days && !$existingSubscription->ends_at) {
                        // Sudah aktif selamanya, tidak perlu diperpanjang (seharusnya tidak sampai sini jika UI benar)
                        Log::info("Paket {$plan->name} untuk user {$user->id} sudah aktif selamanya.");
                    } elseif (!$plan->duration_days && $existingSubscription->ends_at) {
                        $existingSubscription->update(['ends_at' => null, 'status' => 'active']);
                        $message = 'Paket ' . $plan->name . ' Anda sekarang aktif tanpa batas waktu.';
                        $activityDescription = 'Memperpanjang paket ' . $plan->name . ' menjadi tanpa batas waktu melalui pembayaran ' . $payment->merchant_ref . '.';
                    } elseif ($plan->duration_days) {
                        $baseDateForExtension = ($existingSubscription->ends_at && $existingSubscription->ends_at->isFuture())
                            ? $existingSubscription->ends_at
                            : Carbon::now();
                        $newEndsAt = $baseDateForExtension->copy()->addDays($plan->duration_days);

                        $existingSubscription->update(['ends_at' => $newEndsAt, 'status' => 'active']);
                        $message = 'Durasi paket ' . $plan->name . ' Anda telah diperpanjang hingga ' . $newEndsAt->format('d M Y, H:i') . '.';
                        $activityDescription = 'Memperpanjang paket ' . $plan->name . ' hingga ' . $newEndsAt->format('d M Y, H:i') . ' melalui pembayaran ' . $payment->merchant_ref . '.';
                    }
                    $payment->subscription_id = $existingSubscription->id; // Update payment dengan subscription_id
                } else {
                    // KASUS B: Berlangganan baru atau upgrade/downgrade (jika ada logika itu)
                    // Untuk sekarang, asumsikan ini selalu langganan baru jika tidak ada yang aktif untuk plan tersebut
                    $startsAt = Carbon::now();
                    $endsAt = $plan->duration_days ? $startsAt->copy()->addDays($plan->duration_days) : null;

                    $newSubscription = Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                        'status' => 'active',
                    ]);
                    $payment->subscription_id = $newSubscription->id; // Update payment dengan subscription_id baru

                    $message = 'Anda berhasil berlangganan paket ' . $plan->name . '.';
                    $activityDescription = 'Berlangganan paket ' . $plan->name . ' melalui pembayaran ' . $payment->merchant_ref . '.';
                    if ($endsAt) {
                        $message .= ' Aktif hingga ' . $endsAt->format('d M Y, H:i') . '.';
                        $activityDescription .= ' hingga ' . $endsAt->format('d M Y, H:i') . '.';
                    } else {
                        $activityDescription .= ' (tanpa batas waktu).';
                    }
                }
                $payment->save(); // Simpan subscription_id di payment

                // Pencatatan Log Aktivitas untuk Berlangganan
                if (!empty($activityDescription)) {
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'activity_type' => 'subscription_paid',
                        'description' => $activityDescription,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
                // Opsional: Kirim email notifikasi ke user
            } elseif (in_array($payment->status, ['FAILED', 'EXPIRED'])) {
                // Handle jika pembayaran gagal atau kadaluarsa
                ActivityLog::create([
                    'user_id' => $payment->user_id,
                    'activity_type' => 'payment_failed_or_expired',
                    'description' => 'Pembayaran ' . $payment->merchant_ref . ' untuk paket ' . $payment->plan->name . ' ' . strtolower($payment->status) . '.',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            DB::commit();
            Log::info('Tripay Callback: Processed successfully for merchant_ref: ' . $data['merchant_ref']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tripay Callback Error: ' . $e->getMessage(), [
                'merchant_ref' => $data['merchant_ref'] ?? 'N/A',
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function paymentFinish(Request $request)
    {
        // Di sini Anda bisa mengambil status terakhir transaksi dari query string jika ada,
        // atau melakukan query ulang ke database berdasarkan merchant_ref yang disimpan di session/cookie
        // atau dari parameter yang dikirim Tripay.
        // Untuk simple, kita tampilkan pesan umum.
        // Idealnya, Anda akan memiliki merchant_ref di session setelah redirect ke Tripay.
        $merchantRef = $request->session()->get('payment_merchant_ref');
        $status = 'unknown';
        $message = 'Pembayaran Anda sedang diproses. Anda akan mendapatkan notifikasi setelah pembayaran berhasil.';

        if ($merchantRef) {
            $payment = Payment::where('merchant_ref', $merchantRef)->first();
            if ($payment) {
                if ($payment->status === 'PAID') {
                    $status = 'success';
                    $message = 'Pembayaran Anda untuk paket ' . $payment->plan->name . ' telah berhasil!';
                } elseif (in_array($payment->status, ['FAILED', 'EXPIRED'])) {
                    $status = 'failed';
                    $message = 'Pembayaran Anda untuk paket ' . $payment->plan->name . ' gagal atau telah kadaluarsa.';
                } else { // UNPAID atau status lain
                    $status = 'pending';
                    $message = 'Pembayaran Anda untuk paket ' . $payment->plan->name . ' sedang menunggu konfirmasi. Cek instruksi pembayaran jika ada.';
                }
            }
            $request->session()->forget('payment_merchant_ref'); // Hapus dari session
        }


        // Tampilkan halaman status ke pengguna
        return view('payments.finish', compact('status', 'message'));
    }

    public function paymentInstructions(Request $request, $merchantRef)
    {
        $payment = Payment::where('merchant_ref', $merchantRef)
            ->where('user_id', Auth::id()) // Pastikan user hanya bisa lihat instruksi miliknya
            ->where('status', 'UNPAID')
            ->firstOrFail();

        return view('payments.instructions', compact('payment'));
    }
}
