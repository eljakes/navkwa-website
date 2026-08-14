<?php

namespace App\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HubtelGateway implements PaymentGateway
{
    public function initialize(PaymentTransaction $payment): PaymentGatewayResponse
    {
        $endpoint = config('services.hubtel.checkout_endpoint');
        $clientId = config('services.hubtel.client_id');
        $clientSecret = config('services.hubtel.client_secret');
        $accountNumber = config('services.hubtel.account_number');

        if (blank($endpoint) || blank($clientId) || blank($clientSecret) || blank($accountNumber)) {
            throw new RuntimeException('Hubtel checkout credentials are not configured.');
        }

        $payload = [
            'totalAmount' => (float) $payment->amount,
            'description' => $payment->description ?: 'Navkwa Group Ltd. payment',
            'callbackUrl' => route('payments.hubtel.webhook', absolute: true),
            'returnUrl' => route('payments.hubtel.callback', absolute: true),
            'cancellationUrl' => route('payments.create', absolute: true),
            'clientReference' => $payment->reference,
            'merchantAccountNumber' => $accountNumber,
            'currency' => $payment->currency,
            'product' => $payment->product,
            'plan' => $payment->plan,
            'billingCycle' => $payment->billing_cycle,
            'customerName' => $payment->customer_name,
            'customerEmail' => $payment->customer_email,
            'customerPhoneNumber' => $payment->customer_phone,
            'paymentMethod' => $payment->payment_method,
            'mobileNetwork' => $payment->mobile_network,
        ];

        $response = Http::timeout(20)
            ->retry(2, 250)
            ->withBasicAuth($clientId, $clientSecret)
            ->acceptJson()
            ->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new RuntimeException(data_get($response->json(), 'Message')
                ?? data_get($response->json(), 'message')
                ?? 'Hubtel could not initialize this payment.');
        }

        $json = $response->json();
        $checkoutUrl = data_get($json, 'data.checkoutUrl')
            ?? data_get($json, 'Data.CheckoutUrl')
            ?? data_get($json, 'data.authorizationUrl')
            ?? data_get($json, 'Data.AuthorizationUrl')
            ?? data_get($json, 'data.checkout_url')
            ?? data_get($json, 'Data.CheckoutURL')
            ?? data_get($json, 'checkoutUrl')
            ?? data_get($json, 'CheckoutUrl')
            ?? data_get($json, 'checkout_url');

        if (blank($checkoutUrl)) {
            throw new RuntimeException('Hubtel did not return a checkout URL.');
        }

        return new PaymentGatewayResponse(
            checkoutUrl: $checkoutUrl,
            providerReference: data_get($json, 'data.checkoutId')
                ?? data_get($json, 'Data.CheckoutId')
                ?? data_get($json, 'data.transactionId')
                ?? data_get($json, 'Data.TransactionId'),
            payload: $json,
        );
    }
}
