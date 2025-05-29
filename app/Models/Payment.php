<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'merchant_ref',
        'tripay_reference',
        'payment_method_code',
        'payment_method_name',
        'amount',
        'fee_merchant',
        'fee_customer',
        'total_fee',
        'amount_received',
        'pay_code',
        'checkout_url',
        'status',
        'expired_time',
        'note',
        'instructions',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_merchant' => 'decimal:2',
        'fee_customer' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'expired_time' => 'datetime',
        'instructions' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
