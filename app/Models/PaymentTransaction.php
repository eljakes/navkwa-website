<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'reference',
        'provider',
        'payment_method',
        'mobile_network',
        'amount',
        'currency',
        'customer_name',
        'customer_email',
        'customer_phone',
        'description',
        'status',
        'checkout_url',
        'provider_reference',
        'provider_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'provider_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function amountInSubunits(): int
    {
        return (int) round(((float) $this->amount) * 100);
    }

    public function markPaid(?array $payload = null): void
    {
        $this->forceFill([
            'status' => 'paid',
            'provider_payload' => $payload ?: $this->provider_payload,
            'paid_at' => now(),
        ])->save();
    }
}
