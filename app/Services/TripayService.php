<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TripayService
{
    protected $apiKey;
    protected $privateKey;
    protected $merchantCode;
    protected $apiUrl;
    protected $callbackUrl;

    public function __construct()
    {
        $this->apiKey = config('tripay.api_key');
        $this->privateKey = config('tripay.private_key');
        $this->merchantCode = config('tripay.merchant_code');
        $this->apiUrl = rtrim(config('tripay.api_url'), '/');
        $this->callbackUrl = config('tripay.callback_url');
    }

    private function generateSignature(string $merchantRef, int $amount): string
    {
        return hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);
    }

    public function getPaymentChannels()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->apiUrl . '/merchant/payment-channel');

        if ($response->successful() && isset($response->json()['success']) && $response->json()['success']) {
            return $response->json()['data'];
        }
        \Log::error('Tripay Get Channels Failed: ', $response->json() ?? ['message' => $response->body()]);
        return [];
    }

    public function createTransaction(User $user, Plan $plan, string $paymentMethod)
    {
        $merchantRef = 'SUB-' . time() . '-' . Str::random(5); // Atau format lain yang Anda inginkan
        $amount = (int) $plan->price;

        $payload = [
            'method'         => $paymentMethod,
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone_number ?? '', // Tambahkan field phone_number di model User jika perlu
            'order_items'    => [
                [
                    'sku'      => $plan->slug,
                    'name'     => $plan->name,
                    'price'    => $amount,
                    'quantity' => 1,
                ]
            ],
            'callback_url'   => $this->callbackUrl,
            'return_url'     => route('payment.finish'), // Akan kita buat nanti
            'expired_time'   => (time() + (24 * 60 * 60)), // 24 jam
            'signature'      => $this->generateSignature($merchantRef, $amount),
        ];

        \Log::info('Tripay Create Transaction Payload: ', $payload);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->apiUrl . '/transaction/create', $payload);

        \Log::info('Tripay Create Transaction Response: ', $response->json() ?? ['body' => $response->body()]);


        if ($response->successful() && isset($response->json()['success']) && $response->json()['success']) {
            return $response->json()['data'];
        }

        $errorData = $response->json();
        $errorMessage = 'Gagal membuat transaksi Tripay.';
        if (isset($errorData['message'])) {
            $errorMessage .= ' Pesan: ' . $errorData['message'];
        }
        \Log::error('Tripay Create Transaction Failed: ', $errorData ?? ['message' => $response->body()]);
        throw new \Exception($errorMessage);
    }

    public function getTransactionDetail(string $reference)
    {
        $payload = ['reference' => $reference];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->apiUrl . '/transaction/detail', $payload);

        if ($response->successful() && isset($response->json()['success']) && $response->json()['success']) {
            return $response->json()['data'];
        }
        \Log::error('Tripay Get Transaction Detail Failed: ', $response->json() ?? ['message' => $response->body()]);
        return null;
    }

    public function validateCallbackSignature(array $callbackData): bool
    {
        $json = $callbackData['json_data'] ?? file_get_contents('php://input');
        $tripaySignature = $callbackData['signature'] ?? request()->header('X-Callback-Signature');

        if (!$json || !$tripaySignature) {
            \Log::warning('Tripay Callback: Missing JSON data or signature.');
            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::warning('Tripay Callback: Invalid JSON data.');
            return false;
        }

        // Urutan HMAC untuk callback berbeda: privateKey + merchant_code + merchant_ref + total_amount
        // Cek dokumentasi Tripay terbaru jika ini berubah
        // Untuk closed payment: HMAC_SHA256(privateKey + merchantCode + merchantRef + totalAmount)
        // Untuk open payment: HMAC_SHA256(privateKey + reference)
        // Kita asumsikan closed payment karena ada `amount` saat create.
        // Jika menggunakan open payment, sesuaikan signature di bawah.

        // Untuk callback, signature dihitung dari JSON payload yang diterima.
        // Header: X-Callback-Signature
        // Body: JSON
        // Signature: hash_hmac('sha256', json_encode($data), $privateKey)
        // ATAU jika ini adalah callback lama dari open payment
        // Signature: hash_hmac('sha256', $privateKey . $data['reference'], $privateKey)

        // Tripay menyatakan: Validasi signature adalah `hash_hmac('sha256', $json_data_dari_request_body, $privateKey)`
        $signature = hash_hmac('sha256', $json, $this->privateKey);

        if ($signature === $tripaySignature) {
            return true;
        }

        \Log::warning('Tripay Callback: Invalid signature.', [
            'expected_signature' => $signature,
            'received_signature' => $tripaySignature,
            'payload_for_signature' => $json
        ]);
        return false;
    }
}
