<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Demo Checkout - Navkwa Group Ltd.</title>
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="payment-page">
  <main class="payment-status-shell">
    <div class="payment-status-card">
      <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
      <h1 class="font-display">Demo Checkout</h1>
      <p>This payment was created, but live provider credentials are not configured yet.</p>
      <p>Reference: <strong>{{ $payment->reference }}</strong></p>
      <p>Amount: <strong>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</strong></p>
      <div class="payment-status-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('payments.create') }}">Back to payment form</a>
        <a class="btn btn-ghost btn-sm" href="{{ route('home') }}">Return home</a>
      </div>
    </div>
  </main>
</body>
</html>
