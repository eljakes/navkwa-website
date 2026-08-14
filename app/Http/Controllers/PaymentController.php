<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PaymentTransaction;
use App\Payments\PaymentGatewayManager;
use App\Payments\PaystackGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentController extends Controller
{
    public function create()
    {
        return view('payments.create');
    }

    public function navkwaBuildCheckout(Request $request, ?string $plan = null)
    {
        $plans = collect(config('navkwa_build.plans', []))
            ->filter(fn (array $plan) => $plan['checkout_enabled'] ?? false);

        $selectedPlan = $plan ?: $request->query('plan', 'professional');
        if (! $plans->has($selectedPlan)) {
            $selectedPlan = $plans->keys()->first() ?: 'professional';
        }

        $billingCycle = $request->query('billing_cycle', 'monthly');
        if (! in_array($billingCycle, ['monthly', 'annual'], true)) {
            $billingCycle = 'monthly';
        }

        return view('payments.navkwa-build', [
            'plans' => $plans->all(),
            'selectedPlan' => $selectedPlan,
            'billingCycle' => $billingCycle,
            'currency' => config('navkwa_build.currency', 'GHS'),
            'annualBillableMonths' => config('navkwa_build.annual_billable_months', 10),
        ]);
    }

    public function initialize(Request $request, PaymentGatewayManager $gateways)
    {
        $request->merge([
            'provider' => $request->input('provider') ?: config('services.payments.default_provider', 'paystack'),
        ]);

        $validated = $request->validate([
            'provider' => ['required', Rule::in(['paystack', 'hubtel'])],
            'product' => ['nullable', Rule::in(['navkwa_build'])],
            'plan' => ['nullable', 'required_if:product,navkwa_build', Rule::in(array_keys(config('navkwa_build.plans', [])))],
            'billing_cycle' => ['nullable', 'required_if:product,navkwa_build', Rule::in(['monthly', 'annual'])],
            'payment_method' => ['required', Rule::in(['mobile_money', 'card'])],
            'mobile_network' => ['nullable', 'required_if:payment_method,mobile_money', Rule::in(['mtn_momo', 'telecel_cash', 'airteltigo_money'])],
            'amount' => [$request->input('product') === 'navkwa_build' ? 'nullable' : 'required', 'numeric', 'min:1', 'max:1000000'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:180'],
            'customer_phone' => ['nullable', 'required_if:payment_method,mobile_money', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['payment_method'] !== 'mobile_money') {
            $validated['mobile_network'] = null;
        }

        $paymentDetails = $this->resolvePaymentDetails($validated);

        $payment = PaymentTransaction::create([
            ...$validated,
            'reference' => 'NVK-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'amount' => $paymentDetails['amount'],
            'currency' => $paymentDetails['currency'],
            'description' => $paymentDetails['description'],
            'status' => 'pending',
        ]);

        ActivityLog::create([
            'action' => 'Payment checkout initialized',
            'module' => 'Payments',
            'record_type' => PaymentTransaction::class,
            'record_id' => $payment->id,
            'new_values' => $payment->only(['reference', 'provider', 'product', 'plan', 'billing_cycle', 'payment_method', 'amount', 'customer_email']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $response = $gateways->gateway($payment->provider)->initialize($payment);

            $payment->update([
                'checkout_url' => $response->checkoutUrl,
                'provider_reference' => $response->providerReference,
                'provider_payload' => $response->payload,
                'status' => $response->status,
            ]);

            return redirect()->away($response->checkoutUrl);
        } catch (Throwable $exception) {
            $payment->update([
                'status' => 'failed',
                'provider_payload' => ['error' => $exception->getMessage()],
            ]);

            return back()
                ->withInput()
                ->withErrors(['payment' => $exception->getMessage()]);
        }
    }

    public function paystackCallback(Request $request, PaystackGateway $paystack)
    {
        $reference = $request->query('reference');
        $payment = PaymentTransaction::where('reference', $reference)->firstOrFail();
        $payload = $paystack->verify($payment->reference);

        $this->applyPaystackPayload($payment, $payload);

        return view('payments.result', compact('payment'));
    }

    public function paystackWebhook(Request $request, PaystackGateway $paystack)
    {
        if (! $paystack->hasValidWebhookSignature($request->getContent(), $request->header('x-paystack-signature'))) {
            abort(401);
        }

        $reference = $request->input('data.reference');
        $payment = $reference ? PaymentTransaction::where('reference', $reference)->first() : null;

        if ($payment) {
            $this->applyPaystackPayload($payment, $request->all());
        }

        return response()->json(['received' => true]);
    }

    public function hubtelCallback(Request $request)
    {
        $reference = $request->query('clientReference')
            ?? $request->query('ClientReference')
            ?? $request->query('reference');

        $payment = PaymentTransaction::where('reference', $reference)->first();

        if ($payment) {
            $this->applyHubtelPayload($payment, $request->all() ?: $request->query());
        }

        return view('payments.result', [
            'payment' => $payment,
        ]);
    }

    public function hubtelWebhook(Request $request)
    {
        $reference = $request->input('Data.ClientReference')
            ?? $request->input('data.clientReference')
            ?? $request->input('clientReference');

        $payment = PaymentTransaction::where('reference', $reference)->first();

        if ($payment) {
            $this->applyHubtelPayload($payment, $request->all());
        }

        return response()->json(['received' => true]);
    }

    private function resolvePaymentDetails(array $validated): array
    {
        if (($validated['product'] ?? null) !== 'navkwa_build') {
            return [
                'amount' => (float) $validated['amount'],
                'currency' => 'GHS',
                'description' => ($validated['description'] ?? null) ?: 'Navkwa Group Ltd. payment',
            ];
        }

        $planKey = $validated['plan'];
        $plan = config("navkwa_build.plans.{$planKey}");

        if (! $plan || ! ($plan['checkout_enabled'] ?? false) || blank($plan['monthly_amount'])) {
            throw ValidationException::withMessages([
                'plan' => 'This Navkwa Build plan requires a sales quotation before payment.',
            ]);
        }

        $billingCycle = $validated['billing_cycle'] ?? 'monthly';
        $billableMonths = $billingCycle === 'annual' ? max(1, (int) config('navkwa_build.annual_billable_months', 10)) : 1;
        $amount = round(((float) $plan['monthly_amount']) * $billableMonths, 2);
        $cycleLabel = $billingCycle === 'annual' ? 'annual subscription' : 'monthly subscription';

        return [
            'amount' => $amount,
            'currency' => config('navkwa_build.currency', 'GHS'),
            'description' => ($validated['description'] ?? null)
                ?: "Navkwa Build {$plan['name']} {$cycleLabel}",
        ];
    }

    private function applyPaystackPayload(PaymentTransaction $payment, array $payload): void
    {
        $transactionStatus = Str::lower((string) data_get($payload, 'data.status', 'pending'));
        $amountMatches = (int) data_get($payload, 'data.amount') === $payment->amountInSubunits();
        $currencyMatches = blank(data_get($payload, 'data.currency')) || data_get($payload, 'data.currency') === $payment->currency;
        $providerConfirmed = data_get($payload, 'status') === true || data_get($payload, 'event') === 'charge.success';

        if ($providerConfirmed && $transactionStatus === 'success' && $amountMatches && $currencyMatches) {
            $payment->markPaid($payload);
            return;
        }

        $payment->forceFill([
            'status' => $this->normalizeProviderStatus($transactionStatus, $amountMatches && $currencyMatches),
            'provider_reference' => data_get($payload, 'data.reference', $payment->provider_reference),
            'provider_payload' => $payload,
        ])->save();
    }

    private function applyHubtelPayload(PaymentTransaction $payment, array $payload): void
    {
        $providerStatus = $this->hubtelStatus($payload);
        $amount = $this->hubtelAmount($payload);
        $amountMatches = $amount === null || abs($amount - (float) $payment->amount) < 0.01;

        if ($providerStatus === 'paid' && $amountMatches) {
            $payment->markPaid($payload);
            return;
        }

        $payment->forceFill([
            'status' => $this->normalizeProviderStatus($providerStatus, $amountMatches),
            'provider_reference' => data_get($payload, 'Data.TransactionId')
                ?? data_get($payload, 'data.transactionId')
                ?? data_get($payload, 'TransactionId')
                ?? $payment->provider_reference,
            'provider_payload' => $payload,
        ])->save();
    }

    private function hubtelStatus(array $payload): string
    {
        if ((string) data_get($payload, 'ResponseCode') === '0000') {
            return 'paid';
        }

        $status = Str::lower((string) (
            data_get($payload, 'Data.TransactionStatus')
            ?? data_get($payload, 'Data.InvoiceStatus')
            ?? data_get($payload, 'Data.Status')
            ?? data_get($payload, 'data.transactionStatus')
            ?? data_get($payload, 'data.invoiceStatus')
            ?? data_get($payload, 'data.status')
            ?? data_get($payload, 'status')
            ?? 'pending'
        ));

        return match ($status) {
            'success', 'successful', 'paid', 'completed', 'complete' => 'paid',
            'failed', 'failure', 'cancelled', 'canceled', 'declined', 'expired', 'reversed' => 'failed',
            default => 'pending',
        };
    }

    private function hubtelAmount(array $payload): ?float
    {
        $amount = data_get($payload, 'Data.Amount')
            ?? data_get($payload, 'Data.AmountPaid')
            ?? data_get($payload, 'data.amount')
            ?? data_get($payload, 'data.amountPaid')
            ?? data_get($payload, 'amount')
            ?? data_get($payload, 'amountPaid');

        return is_numeric($amount) ? (float) $amount : null;
    }

    private function normalizeProviderStatus(string $status, bool $amountMatches = true): string
    {
        if (! $amountMatches) {
            return 'failed';
        }

        return match (Str::lower($status)) {
            'success', 'successful', 'paid', 'completed', 'complete' => 'paid',
            'failed', 'failure', 'abandoned', 'cancelled', 'canceled', 'declined', 'expired', 'reversed' => 'failed',
            'ongoing', 'processing', 'queued' => 'pending',
            default => 'pending',
        };
    }
}
