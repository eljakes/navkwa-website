<?php

namespace App\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PaystackGateway implements PaymentGateway
{
    public function initialize(PaymentTransaction $payment): PaymentGatewayResponse
    {
        $secretKey = config('services.paystack.secret_key');
        $baseUrl = rtrim(config('services.paystack.base_url'), '/');

        // PAYSTACK INTEGRATION POINT:
        // This uses Paystack's hosted Checkout initialize endpoint. Keep card
        // details off this server; Paystack collects Visa/Mastercard details.
        if (blank($secretKey)) {
            return $this->demoResponse($payment, 'Missing PAYSTACK_SECRET_KEY');
        }

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->post($baseUrl.'/transaction/initialize', [
                'email' => $payment->customer_email,
                'amount' => (string) $payment->amountInSubunits(),
                'currency' => $payment->currency,
                'reference' => $payment->reference,
                'callback_url' => route('payments.paystack.callback'),
                'channels' => [$payment->payment_method === 'card' ? 'card' : 'mobile_money'],
                'metadata' => [
                    'customer_name' => $payment->customer_name,
                    'customer_phone' => $payment->customer_phone,
                    'mobile_network' => $payment->mobile_network,
                    'description' => $payment->description,
                ],
            ]);

        if (! $response->successful() || ! data_get($response->json(), 'status')) {
            throw new RuntimeException(data_get($response->json(), 'message', 'Paystack could not initialize this payment.'));
        }

        return new PaymentGatewayResponse(
            checkoutUrl: data_get($response->json(), 'data.authorization_url'),
            providerReference: data_get($response->json(), 'data.reference'),
            payload: $response->json(),
        );
    }

    public function verify(string $reference): array
    {
        $secretKey = config('services.paystack.secret_key');
        $baseUrl = rtrim(config('services.paystack.base_url'), '/');

        if (blank($secretKey)) {
            return ['status' => false, 'message' => 'PAYSTACK_SECRET_KEY is not configured.'];
        }

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->get($baseUrl.'/transaction/verify/'.$reference);

        return $response->json();
    }

    public function hasValidWebhookSignature(string $payload, ?string $signature): bool
    {
        $secretKey = config('services.paystack.secret_key');

        return filled($secretKey)
            && filled($signature)
            && hash_equals(hash_hmac('sha512', $payload, $secretKey), $signature);
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
