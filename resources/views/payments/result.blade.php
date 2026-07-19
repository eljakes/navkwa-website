<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Status - Navkwa Group Ltd.</title>
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="payment-page">
  <main class="payment-status-shell">
    <div class="payment-status-card">
      <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
      @if($payment)
        <h1 class="font-display">Payment {{ ucfirst($payment->status) }}</h1>
        <p>Reference: <strong>{{ $payment->reference }}</strong></p>
        <p>Amount: <strong>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</strong></p>
      @else
        <h1 class="font-display">Payment Pending</h1>
        <p>We could not match this callback to a local payment record. Check the provider dashboard and transaction reference.</p>
      @endif
      <div class="payment-status-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('payments.create') }}">Make another payment</a>
        <a class="btn btn-ghost btn-sm" href="{{ route('home') }}">Return home</a>
      </div>
    </div>
  </main>
</body>
</html>
