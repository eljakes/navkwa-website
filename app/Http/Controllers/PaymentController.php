<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Payments\HubtelGateway;
use App\Payments\PaymentGatewayManager;
use App\Payments\PaystackGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class PaymentController extends Controller
{
    public function create()
    {
        return view('payments.create');
    }

    public function initialize(Request $request, PaymentGatewayManager $gateways)
    {
        $validated = $request->validate([
            'provider' => ['required', Rule::in(['paystack', 'hubtel'])],
            'payment_method' => ['required', Rule::in(['mobile_money', 'card'])],
            'mobile_network' => ['nullable', 'required_if:payment_method,mobile_money', Rule::in(['mtn_momo', 'telecel_cash', 'airteltigo_money'])],
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:180'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $payment = PaymentTransaction::create([
            ...$validated,
            'reference' => 'NVK-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'currency' => 'GHS',
            'status' => 'pending',
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

    public function demo(PaymentTransaction $payment)
    {
        return view('payments.demo', compact('payment'));
    }

    public function paystackCallback(Request $request, PaystackGateway $paystack)
    {
        $reference = $request->query('reference');
        $payment = PaymentTransaction::where('reference', $reference)->firstOrFail();
        $payload = $paystack->verify($payment->reference);

        $isPaid = data_get($payload, 'status') === true
            && data_get($payload, 'data.status') === 'success'
            && (int) data_get($payload, 'data.amount') === $payment->amountInSubunits();

        $payment->forceFill([
            'status' => $isPaid ? 'paid' : data_get($payload, 'data.status', 'pending'),
            'provider_payload' => $payload,
            'paid_at' => $isPaid ? now() : $payment->paid_at,
        ])->save();

        return view('payments.result', compact('payment'));
    }

    public function paystackWebhook(Request $request, PaystackGateway $paystack)
    {
        if (! $paystack->hasValidWebhookSignature($request->getContent(), $request->header('x-paystack-signature'))) {
            abort(401);
        }

        if ($request->input('event') === 'charge.success') {
            $reference = $request->input('data.reference');
            $payment = PaymentTransaction::where('reference', $reference)->first();

            if ($payment && (int) $request->input('data.amount') === $payment->amountInSubunits()) {
                $payment->markPaid($request->all());
            }
        }

        return response()->json(['received' => true]);
    }

    public function hubtelCallback(Request $request)
    {
        $reference = $request->query('clientReference')
            ?? $request->query('ClientReference')
            ?? $request->query('reference');

        $payment = PaymentTransaction::where('reference', $reference)->first();

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
            $success = $request->input('ResponseCode') === '0000'
                || Str::lower((string) $request->input('Data.TransactionStatus')) === 'success'
                || Str::lower((string) $request->input('Data.InvoiceStatus')) === 'success';

            $payment->forceFill([
                'status' => $success ? 'paid' : 'pending',
                'provider_payload' => $request->all(),
                'paid_at' => $success ? now() : $payment->paid_at,
            ])->save();
        }

        return response()->json(['received' => true]);
    }
}
