<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'reference',
        'provider',
        'product',
        'plan',
        'billing_cycle',
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
            'paid_at' => $this->paid_at ?: now(),
        ])->save();
    }

    public function planName(): ?string
    {
        if ($this->product !== 'navkwa_build' || blank($this->plan)) {
            return null;
        }

        return config("navkwa_build.plans.{$this->plan}.name");
    }

    public function productLabel(): string
    {
        return $this->product === 'navkwa_build' ? 'Navkwa Build subscription' : 'General payment';
    }

    public function subscriptionLabel(): string
    {
        $planName = $this->planName();

        if (! $planName) {
            return 'Not applicable';
        }

        return trim($planName.' '.($this->billing_cycle ? '('.ucfirst($this->billing_cycle).')' : ''));
    }
}
