<?php

namespace App\Payments;

use Illuminate\Validation\ValidationException;

class PaymentGatewayManager
{
    public function gateway(string $provider): PaymentGateway
    {
        return match ($provider) {
            'paystack' => app(PaystackGateway::class),
            'hubtel' => app(HubtelGateway::class),
            default => throw ValidationException::withMessages([
                'provider' => 'Choose either Paystack or Hubtel.',
            ]),
        };
    }
}
