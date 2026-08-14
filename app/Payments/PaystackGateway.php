<?php

namespace App\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackGateway implements PaymentGateway
{
    public function initialize(PaymentTransaction $payment): PaymentGatewayResponse
    {
        $secretKey = config('services.paystack.secret_key');
        $baseUrl = rtrim(config('services.paystack.base_url'), '/');

        if (blank($secretKey)) {
            throw new RuntimeException('PAYSTACK_SECRET_KEY is not configured.');
        }

        $response = Http::timeout(20)
            ->retry(2, 250)
            ->withToken($secretKey)
            ->acceptJson()
            ->post($baseUrl.'/transaction/initialize', [
                'email' => $payment->customer_email,
                'amount' => (string) $payment->amountInSubunits(),
                'currency' => $payment->currency,
                'reference' => $payment->reference,
                'callback_url' => route('payments.paystack.callback', absolute: true),
                'channels' => [$payment->payment_method === 'card' ? 'card' : 'mobile_money'],
                'metadata' => json_encode([
                    'product' => $payment->product,
                    'plan' => $payment->plan,
                    'billing_cycle' => $payment->billing_cycle,
                    'customer_name' => $payment->customer_name,
                    'customer_phone' => $payment->customer_phone,
                    'mobile_network' => $payment->mobile_network,
                    'description' => $payment->description,
                ]),
            ]);

        if (! $response->successful() || ! data_get($response->json(), 'status')) {
            throw new RuntimeException(data_get($response->json(), 'message', 'Paystack could not initialize this payment.'));
        }

        if (blank(data_get($response->json(), 'data.authorization_url'))) {
            throw new RuntimeException('Paystack did not return an authorization URL.');
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
            throw new RuntimeException('PAYSTACK_SECRET_KEY is not configured.');
        }

        $response = Http::timeout(20)
            ->retry(2, 250)
            ->withToken($secretKey)
            ->acceptJson()
            ->get($baseUrl.'/transaction/verify/'.$reference);

        if (! $response->successful()) {
            throw new RuntimeException(data_get($response->json(), 'message', 'Paystack could not verify this payment.'));
        }

        return $response->json();
    }

    public function hasValidWebhookSignature(string $payload, ?string $signature): bool
    {
        $secretKey = config('services.paystack.secret_key');

        return filled($secretKey)
            && filled($signature)
            && hash_equals(hash_hmac('sha512', $payload, $secretKey), $signature);
    }
}
