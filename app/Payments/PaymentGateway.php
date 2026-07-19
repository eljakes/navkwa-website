<?php

namespace App\Payments;

use App\Models\PaymentTransaction;

interface PaymentGateway
{
    public function initialize(PaymentTransaction $payment): PaymentGatewayResponse;
}
