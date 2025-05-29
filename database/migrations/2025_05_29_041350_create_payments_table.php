<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null'); // Akan diisi setelah langganan dibuat/diperbarui
            $table->string('merchant_ref')->unique(); // Referensi unik dari sisi kita
            $table->string('tripay_reference')->nullable()->unique(); // Referensi dari Tripay
            $table->string('payment_method_code')->nullable();
            $table->string('payment_method_name')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee_merchant', 15, 2)->default(0);
            $table->decimal('fee_customer', 15, 2)->default(0);
            $table->decimal('total_fee', 15, 2)->default(0);
            $table->decimal('amount_received', 15, 2)->default(0);
            $table->string('pay_code')->nullable(); // Kode bayar (VA, dll)
            $table->text('checkout_url')->nullable(); // URL redirect ke Tripay
            $table->string('status', 20)->default('UNPAID'); // UNPAID, PAID, FAILED, EXPIRED
            $table->timestamp('expired_time')->nullable();
            $table->text('note')->nullable();
            $table->json('instructions')->nullable(); // Instruksi pembayaran dari Tripay
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
