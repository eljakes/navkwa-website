<?php

namespace App\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class HubtelGateway implements PaymentGateway
{
    public function initialize(PaymentTransaction $payment): PaymentGatewayResponse
    {
        $endpoint = config('services.hubtel.checkout_endpoint');
        $clientId = config('services.hubtel.client_id');
        $clientSecret = config('services.hubtel.client_secret');

        if (blank($endpoint) || blank($clientId) || blank($clientSecret)) {
            return $this->demoResponse($payment, 'Hubtel credentials or checkout endpoint are not configured.');
        }

        // HUBTEL INTEGRATION POINT:
        // Confirm the exact merchant checkout endpoint and payload keys from the
        // Hubtel developer portal for this merchant account, then adjust only this
        // payload/parser if Hubtel provides account-specific field names.
        $payload = [
            'totalAmount' => (float) $payment->amount,
            'description' => $payment->description ?: 'Navkwa Group Ltd. payment',
            'callbackUrl' => route('payments.hubtel.webhook'),
            'returnUrl' => route('payments.hubtel.callback'),
            'cancellationUrl' => route('payments.create'),
            'clientReference' => $payment->reference,
            'merchantAccountNumber' => config('services.hubtel.account_number'),
            'customerName' => $payment->customer_name,
            'customerEmail' => $payment->customer_email,
            'customerPhoneNumber' => $payment->customer_phone,
            'paymentMethod' => $payment->payment_method,
            'mobileNetwork' => $payment->mobile_network,
        ];

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->acceptJson()
            ->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new RuntimeException(data_get($response->json(), 'Message', 'Hubtel could not initialize this payment.'));
        }

        $json = $response->json();
        $checkoutUrl = data_get($json, 'data.checkoutUrl')
            ?? data_get($json, 'Data.CheckoutUrl')
            ?? data_get($json, 'data.authorizationUrl')
            ?? data_get($json, 'Data.AuthorizationUrl')
            ?? data_get($json, 'checkoutUrl')
            ?? data_get($json, 'CheckoutUrl');

        if (blank($checkoutUrl)) {
            throw new RuntimeException('Hubtel response did not include a checkout URL. Update app/Payments/HubtelGateway.php to match the merchant response payload.');
        }

        return new PaymentGatewayResponse(
            checkoutUrl: $checkoutUrl,
            providerReference: data_get($json, 'data.checkoutId') ?? data_get($json, 'Data.CheckoutId'),
            payload: $json,
        );
    }

    private function demoResponse(PaymentTransaction $payment, string $reason): PaymentGatewayResponse
    {
        return new PaymentGatewayResponse(
            checkoutUrl: route('payments.demo', $payment),
            providerReference: 'demo-'.Str::lower($payment->reference),
            payload: ['mode' => 'demo', 'reason' => $reason],
            status: 'demo',
        );
    }
}
