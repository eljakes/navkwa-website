<?php

namespace App\Payments;

class PaymentGatewayResponse
{
    public function __construct(
        public readonly string $checkoutUrl,
        public readonly ?string $providerReference = null,
        public readonly array $payload = [],
        public readonly string $status = 'pending',
    ) {
    }
}
